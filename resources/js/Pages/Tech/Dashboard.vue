<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    stats: { type: Object, required: true },
    tickets: { type: Array, required: true },
})

const user = computed(() => usePage().props.auth.user)
const statusFilter = ref('')
const searchQuery = ref('')

const statusBadgeClass = (status) => ({
    'Ouvert':'badge-ouvert', 'En cours':'badge-en-cours', 'Rapport en rédaction':'badge-rapport', 'Résolu':'badge-resolu', 'Fermé':'badge-ferme',
}[status] || 'badge-ferme')

const priorityBadgeClass = (p) => ({
    'Critique':'badge-critique', 'Haute':'badge-haute', 'Moyenne':'badge-moyenne', 'Basse':'badge-basse',
}[p] || 'badge-basse')

const filteredTickets = computed(() => {
    return props.tickets.filter(t => {
        if (statusFilter.value && t.statut !== statusFilter.value) return false
        return true
    })
})

function hoursUntil(deadline) {
    if (!deadline) return Infinity
    return (new Date(deadline).getTime() - Date.now()) / 3600000
}

function slaState(hours) {
    if (hours < 0) return { label: 'Dépassé', cls: 'text-[#ff6b7a]' }
    if (hours < 2) return { label: `${Math.max(0, Math.floor(hours * 60))} min`, cls: 'text-[#ffb86a]' }
    if (hours < 24) return { label: `${hours.toFixed(1)} h`, cls: 'text-[#ffcf6a]' }
    return { label: `${Math.floor(hours / 24)} j`, cls: 'text-muted' }
}

function isUrgent(ticket) {
    const h = hoursUntil(ticket.deadline)
    return h < 2 && ticket.statut !== 'Résolu' && ticket.statut !== 'Fermé'
}

function timeAgo(dateStr) {
    if (!dateStr) return ''
    const s = (Date.now() - new Date(dateStr).getTime()) / 1000
    if (s < 60) return "à l'instant"
    const m = Math.floor(s / 60)
    if (m < 60) return `il y a ${m} min`
    const h = Math.floor(m / 60)
    if (h < 48) return `il y a ${h} h`
    const d = Math.floor(h / 24)
    return `il y a ${d} j`
}

function exportCSV() {
    const headers = ['Réf', 'Titre', 'Client', 'Statut', 'Priorité', 'Catégorie', 'Date']
    const rows = props.tickets.map(t => [t.ref, t.titre, t.client?.name || '', t.statut, t.priorite, t.categorie || '', t.created_at])
    const csv = [headers.join(','), ...rows.map(r => r.map(v => `"${v || ''}"`).join(','))].join('\n')
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' })
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'tickets-tech.csv'; a.click()
}
</script>

<template>
    <Head title="Console Technicien" />
    <AppLayout>
        <div class="space-y-7">
            <div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-[#6bcbff] font-semibold mb-2 section-title">SOC CONSOLE</div>
                <h2 class="section-title text-[28px] text-main">Mes tickets assignés</h2>
                <p class="text-[13px] text-sub mt-1">Bienvenue {{ user?.name }} &bull; Shift SOC actif</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4">
                    <div class="w-[46px] h-[46px] rounded-xl bg-[#0b3b6c] border border-[#2577cc]/40 flex items-center justify-center text-[#63caff] font-bold text-lg section-title">{{ stats.assignes }}</div>
                    <div>
                        <p class="text-[11px] text-muted uppercase tracking-wider font-semibold">Assignés</p>
                        <p class="text-[22px] section-title font-bold text-main">{{ stats.assignes }}</p>
                    </div>
                </div>
                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4">
                    <div class="w-[46px] h-[46px] rounded-xl bg-[#312210] border border-[#ffa83d]/30 flex items-center justify-center text-[#ffc168] font-bold text-lg section-title">{{ stats.en_cours }}</div>
                    <div>
                        <p class="text-[11px] text-muted uppercase tracking-wider font-semibold">En cours</p>
                        <p class="text-[22px] section-title font-bold text-main">{{ stats.en_cours }}</p>
                    </div>
                </div>
                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4">
                    <div class="w-[46px] h-[46px] rounded-xl bg-[#0d2b22] border border-[#24e0b1]/30 flex items-center justify-center text-[#46f1bd] font-bold text-lg section-title">{{ stats.resolus }}</div>
                    <div>
                        <p class="text-[11px] text-muted uppercase tracking-wider font-semibold">Résolus</p>
                        <p class="text-[22px] section-title font-bold text-main">{{ stats.resolus }}</p>
                    </div>
                </div>
            </div>

            <div class="cyber-card rounded-[20px] p-5">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <h3 class="section-title text-[17px] text-main">File active</h3>
                    <div class="flex items-center gap-3">
                        <select v-model="statusFilter" class="field !py-[8px] !w-auto text-[13px]">
                            <option value="">Tous les statuts</option>
                            <option v-for="s in ['Ouvert','En cours','Rapport en rédaction','Résolu','Fermé']" :key="s" :value="s">{{ s }}</option>
                        </select>
                        <button @click="exportCSV" class="rounded-xl border border-subtle bg-[#0f1f35] text-sub px-4 py-[7px] text-[12px] font-semibold hover:border-[#2ea8ff] hover:text-main transition-colors">
                            <svg class="w-[14px] h-[14px] inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Exporter CSV
                        </button>
                    </div>
                </div>

                <div class="space-y-3">
                    <div v-for="ticket in filteredTickets" :key="ticket.id"
                        @click="router.get(route('tickets.show', ticket.id))"
                        class="cyber-card rounded-[16px] p-5 cursor-pointer hover-lift"
                        :class="{ 'ring-1 ring-[#ff5b6a]/30': isUrgent(ticket) }">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="mono text-[13px] text-[#6bcbff] font-semibold tracking-wider shrink-0">{{ ticket.ref }}</span>
                                <span class="text-[13px] text-sub truncate">{{ ticket.client?.name || '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span :class="statusBadgeClass(ticket.statut)">{{ ticket.statut }}</span>
                                <span :class="priorityBadgeClass(ticket.priorite)">{{ ticket.priorite }}</span>
                            </div>
                        </div>
                        <h4 class="text-[15px] font-semibold text-main mt-2 mb-2">{{ ticket.titre }}</h4>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[12px] text-sub">
                            <span>Cat: <span class="text-sub font-medium">{{ ticket.categorie }}</span></span>
                            <span v-if="ticket.deadline" :class="slaState(hoursUntil(ticket.deadline)).cls">
                                {{ isUrgent(ticket) ? '⚠ ' : '' }}Échéance {{ slaState(hoursUntil(ticket.deadline)).label }}
                            </span>
                            <span class="text-muted">{{ timeAgo(ticket.updated_at || ticket.created_at) }}</span>
                        </div>
                    </div>
                    <div v-if="filteredTickets.length === 0" class="py-12 text-center text-muted text-[14px]">Aucun ticket assigné.</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

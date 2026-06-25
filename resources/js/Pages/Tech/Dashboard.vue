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

const statusBadgeClass = (status) => ({
    'Ouvert': 'badge-ouvert',
    'En cours': 'badge-en-cours',
    'Rapport en rédaction': 'badge-rapport',
    'Résolu': 'badge-resolu',
    'Fermé': 'badge-ferme',
}[status] || 'badge-ferme')

const priorityBadgeClass = (priority) => ({
    'Critique': 'badge-critique',
    'Haute': 'badge-haute',
    'Moyenne': 'badge-moyenne',
    'Basse': 'badge-basse',
}[priority] || 'badge-basse')

const filteredTickets = computed(() => {
    if (!statusFilter.value) return props.tickets
    return props.tickets.filter(t => t.statut.toLowerCase() === statusFilter.value.toLowerCase())
})

function isDeadlineClose(ticket) {
    if (!ticket.sla_deadline) return false
    const deadline = new Date(ticket.sla_deadline)
    const now = new Date()
    const diffHours = (deadline - now) / (1000 * 60 * 60)
    return diffHours < 2 && ticket.statut !== 'Résolu' && ticket.statut !== 'Fermé'
}

function timeAgo(dateString) {
    if (!dateString) return ''
    const now = new Date()
    const date = new Date(dateString)
    const diffMs = now - date
    const diffMins = Math.floor(diffMs / 60000)
    if (diffMins < 1) return "À l'instant"
    if (diffMins < 60) return `Il y a ${diffMins} min`
    const diffHours = Math.floor(diffMins / 60)
    if (diffHours < 24) return `Il y a ${diffHours}h`
    const diffDays = Math.floor(diffHours / 24)
    return `Il y a ${diffDays}j`
}

function exportCSV() {
    const headers = ['Réf', 'Titre', 'Client', 'Statut', 'Priorité', 'Catégorie', 'Date']
    const rows = props.tickets.map(t => [
        t.ref, t.titre, t.client?.name || '', t.statut, t.priorite, t.categorie || '', t.created_at
    ])
    const csv = [headers.join(','), ...rows.map(r => r.map(v => `"${v || ''}"`).join(','))].join('\n')
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'tickets-tech.csv'
    a.click()
    URL.revokeObjectURL(url)
}
</script>

<template>
    <Head title="Console Technicien" />

    <AppLayout>
        <div class="space-y-7">
            <div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-[#6bcbff] font-semibold mb-2 font-['Space_Grotesk']">SOC CONSOLE</div>
                <h2 class="section-title text-[28px] text-[#dde7f6]">Mes tickets assignés</h2>
                <p class="text-[13px] text-[#a7bed8] mt-1">Bienvenue {{ user?.name }} &bull; Shift SOC actif</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4">
                    <div class="w-[46px] h-[46px] rounded-xl bg-[#0b3b6c] border border-[#2577cc]/40 flex items-center justify-center text-[#63caff] font-bold text-lg font-['Space_Grotesk']">
                        {{ stats.assignes }}
                    </div>
                    <div>
                        <p class="text-[11px] text-[#7f9dbc] uppercase tracking-wider font-semibold">Assignés</p>
                        <p class="text-[22px] font-['Space_Grotesk'] font-bold text-[#dde7f6]">{{ stats.assignes }}</p>
                    </div>
                </div>
                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4">
                    <div class="w-[46px] h-[46px] rounded-xl bg-[#312210] border border-[#ffa83d]/30 flex items-center justify-center text-[#ffc168] font-bold text-lg font-['Space_Grotesk']">
                        {{ stats.en_cours }}
                    </div>
                    <div>
                        <p class="text-[11px] text-[#7f9dbc] uppercase tracking-wider font-semibold">En cours</p>
                        <p class="text-[22px] font-['Space_Grotesk'] font-bold text-[#dde7f6]">{{ stats.en_cours }}</p>
                    </div>
                </div>
                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4">
                    <div class="w-[46px] h-[46px] rounded-xl bg-[#0d2b22] border border-[#24e0b1]/30 flex items-center justify-center text-[#46f1bd] font-bold text-lg font-['Space_Grotesk']">
                        {{ stats.resolus }}
                    </div>
                    <div>
                        <p class="text-[11px] text-[#7f9dbc] uppercase tracking-wider font-semibold">Résolus</p>
                        <p class="text-[22px] font-['Space_Grotesk'] font-bold text-[#dde7f6]">{{ stats.resolus }}</p>
                    </div>
                </div>
            </div>

            <div class="cyber-card rounded-[20px] p-5">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <h3 class="section-title text-[17px] text-[#dde7f6]">File active</h3>
                    <div class="flex items-center gap-3">
                        <select v-model="statusFilter" class="field !py-[8px] !w-auto text-[13px]">
                            <option value="">Tous les statuts</option>
                            <option value="Ouvert">Ouvert</option>
                            <option value="En cours">En cours</option>
                            <option value="Rapport en rédaction">Rapport en rédaction</option>
                            <option value="Résolu">Résolu</option>
                            <option value="Fermé">Fermé</option>
                        </select>
                        <button
                            @click="exportCSV"
                            class="rounded-xl border border-[#1d3658] bg-[#0f1f35] text-[#a7bed8] px-4 py-[7px] text-[12px] font-semibold hover:border-[#2ea8ff] hover:text-[#dde7f6] transition-colors duration-200"
                        >
                            <svg class="w-[14px] h-[14px] inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Exporter CSV
                        </button>
                    </div>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="ticket in filteredTickets"
                        :key="ticket.id"
                        @click="router.get(route('tickets.show', ticket.id))"
                        class="cyber-card rounded-[16px] p-5 cursor-pointer hover-lift"
                        :class="{ 'ring-1 ring-[#ff5b6a]/30': isDeadlineClose(ticket) }"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="mono text-[13px] text-[#6bcbff] font-semibold tracking-wider shrink-0">{{ ticket.ref }}</span>
                                <span class="text-[13px] text-[#a7bed8] truncate">{{ ticket.client?.name || '—' }}</span>
                            </div>
                            <div v-if="isDeadlineClose(ticket)" class="text-[11px] font-bold text-[#ff5b6a] flex items-center gap-1">
                                <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                                ⚠ SLA critique
                            </div>
                        </div>
                        <h4 class="text-[15px] font-semibold text-[#dde7f6] mt-2 mb-3">{{ ticket.titre }}</h4>
                        <div class="flex flex-wrap items-center gap-2">
                            <span :class="statusBadgeClass(ticket.statut)">{{ ticket.statut }}</span>
                            <span :class="priorityBadgeClass(ticket.priorite)">{{ ticket.priorite }}</span>
                            <span class="chip bg-[#1a2538] text-[#8aa4cc]">{{ ticket.categorie || '—' }}</span>
                            <span v-if="ticket.sla_deadline" class="text-[11px] text-[#556e8c] font-medium ml-auto">
                                SLA {{ ticket.sla_deadline }}
                            </span>
                            <span class="text-[11px] text-[#556e8c]">{{ timeAgo(ticket.created_at) }}</span>
                        </div>
                    </div>

                    <div v-if="filteredTickets.length === 0" class="py-12 text-center text-[#556e8c] text-[14px]">
                        Aucun ticket assigné.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

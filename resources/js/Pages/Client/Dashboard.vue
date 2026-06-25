<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    stats: { type: Object, required: true },
    tickets: { type: Array, required: true },
})

const search = ref('')
const statusFilter = ref('')
const categoryFilter = ref('')

const filteredTickets = computed(() => {
    let list = props.tickets
    if (search.value) {
        const q = search.value.toLowerCase()
        list = list.filter(t =>
            (t.ref?.toLowerCase().includes(q)) ||
            (t.titre?.toLowerCase().includes(q))
        )
    }
    if (statusFilter.value) {
        list = list.filter(t => t.statut === statusFilter.value)
    }
    if (categoryFilter.value) {
        list = list.filter(t => t.categorie === categoryFilter.value)
    }
    return list
})

const statuses = ['', 'Ouvert', 'En cours', 'Rapport en rédaction', 'Résolu', 'Fermé']
const categories = ['', 'Réseau', 'Logiciel', 'Matériel', 'Électrique', 'Autre']

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

function timeAgo(dateStr) {
    if (!dateStr) return '—'
    const now = new Date()
    const then = new Date(dateStr)
    const diff = Math.floor((now - then) / 1000)
    if (diff < 60) return `il y a ${diff}s`
    if (diff < 3600) return `il y a ${Math.floor(diff / 60)}min`
    if (diff < 86400) return `il y a ${Math.floor(diff / 3600)}h`
    if (diff < 2592000) return `il y a ${Math.floor(diff / 86400)}j`
    return `il y a ${Math.floor(diff / 2592000)}mois`
}

function exportCSV() {
    window.location.href = route('tickets.export')
}
</script>

<template>
    <Head title="Mes interventions ATECH" />

    <AppLayout>
        <div class="space-y-7">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5">
                <div>
                    <div class="text-[11px] uppercase tracking-[0.2em] text-[#69b9ff] font-semibold mb-1.5">Client Portal</div>
                    <h2 class="section-title text-[28px] text-[#dcefff] leading-tight">Mes interventions ATECH</h2>
                    <p class="text-[#7d95b6] text-[13px] mt-1.5 font-medium tracking-wide">Suivi temps réel &bull; SOC 24/7</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="exportCSV"
                        class="inline-flex items-center gap-2 rounded-xl border border-[#1d3658] bg-transparent text-[#91a9c6] px-4 py-2.5 text-[13px] font-medium hover:border-[#2ea8ff] hover:text-[#dcefff] transition-colors duration-200"
                    >
                        <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Exporter CSV
                    </button>
                    <Link
                        :href="route('tickets.create')"
                        class="btn-cyber"
                    >
                        <svg class="w-[15px] h-[15px] mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        + Nouveau ticket
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4 hover-lift">
                    <div class="w-[52px] h-[52px] rounded-[14px] bg-[#0d2240] border border-[#2a5a8a] flex items-center justify-center shrink-0">
                        <svg class="w-[22px] h-[22px] text-[#5ec8ff]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <div>
                        <div class="text-[11px] uppercase tracking-[0.12em] text-[#7d95b6] font-semibold mb-0.5">Ouverts</div>
                        <div class="text-[30px] font-['Space_Grotesk'] font-extrabold text-[#5ec8ff] leading-none">{{ props.stats.ouverts }}</div>
                    </div>
                </div>

                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4 hover-lift">
                    <div class="w-[52px] h-[52px] rounded-[14px] bg-[#2a1f0d] border border-[#5c471a] flex items-center justify-center shrink-0">
                        <svg class="w-[22px] h-[22px] text-[#ffbe63]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <div>
                        <div class="text-[11px] uppercase tracking-[0.12em] text-[#7d95b6] font-semibold mb-0.5">En cours</div>
                        <div class="text-[30px] font-['Space_Grotesk'] font-extrabold text-[#ffbe63] leading-none">{{ props.stats.en_cours }}</div>
                    </div>
                </div>

                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4 hover-lift">
                    <div class="w-[52px] h-[52px] rounded-[14px] bg-[#0b2b20] border border-[#1e5c44] flex items-center justify-center shrink-0">
                        <svg class="w-[22px] h-[22px] text-[#42f0b6]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <div class="text-[11px] uppercase tracking-[0.12em] text-[#7d95b6] font-semibold mb-0.5">Résolus</div>
                        <div class="text-[30px] font-['Space_Grotesk'] font-extrabold text-[#42f0b6] leading-none">{{ props.stats.resolus }}</div>
                    </div>
                </div>
            </div>

            <div class="cyber-card rounded-[20px] p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                    <h3 class="section-title text-[18px] text-[#dcefff]">Mes tickets</h3>
                    <div class="flex flex-wrap items-center gap-3">
                        <select
                            v-model="statusFilter"
                            class="field !py-[8px] !w-auto !text-[13px]"
                        >
                            <option value="">Tous les statuts</option>
                            <option v-for="s in statuses.slice(1)" :key="s" :value="s">{{ s }}</option>
                        </select>
                        <select
                            v-model="categoryFilter"
                            class="field !py-[8px] !w-auto !text-[13px]"
                        >
                            <option value="">Toutes catégories</option>
                            <option v-for="c in categories.slice(1)" :key="c" :value="c">{{ c }}</option>
                        </select>
                    </div>
                </div>

                <div v-if="filteredTickets.length === 0" class="text-center py-14 text-[#556e8c] text-[14px]">
                    Aucun ticket trouvé.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="ticket in filteredTickets"
                        :key="ticket.id"
                        @click="router.get(route('tickets.show', ticket.id))"
                        class="cyber-card rounded-[16px] p-5 hover-lift cursor-pointer"
                    >
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <span class="mono text-[12px] text-[#69b9ff] font-semibold tracking-wider">{{ ticket.ref }}</span>
                            <span :class="priorityBadgeClass(ticket.priorite)">{{ ticket.priorite }}</span>
                        </div>
                        <h4 class="text-[15px] font-semibold text-[#dcefff] leading-tight mb-2">{{ ticket.titre }}</h4>
                        <p class="text-[13px] text-[#7d95b6] leading-relaxed line-clamp-2 mb-4">
                            {{ ticket.description }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3 text-[11px] text-[#556e8c]">
                            <span :class="statusBadgeClass(ticket.statut)">{{ ticket.statut }}</span>
                            <span class="text-[#3d5375]">&bull;</span>
                            <span>{{ ticket.categorie }}</span>
                            <span class="text-[#3d5375]">&bull;</span>
                            <span class="text-[#69b9ff]">{{ ticket.technicien?.name || 'Non assigné' }}</span>
                            <span class="text-[#3d5375]">&bull;</span>
                            <span>SLA</span>
                            <span class="text-[#3d5375]">&bull;</span>
                            <span>{{ timeAgo(ticket.created_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

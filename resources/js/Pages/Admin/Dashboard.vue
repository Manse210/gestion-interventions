<script setup>
import { computed, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
    Chart as ChartJS,
    CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement,
    Tooltip, Legend, Filler
} from 'chart.js'
import { Bar, Line, Doughnut } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Tooltip, Legend, Filler)

const props = defineProps({
    period: { type: String, default: 'Mois' },
    periodLabel: { type: Array, default: () => [] },
    lineChartData: { type: Array, default: () => [] },
    stats: { type: Object, required: true },
    ticketsParPriorite: { type: Array, default: () => [] },
    ticketsParCategorie: { type: Array, default: () => [] },
    performanceTechniciens: { type: Array, default: () => [] },
    ticketsRecents: { type: Array, default: () => [] },
})

function changePeriod(p) {
    router.get(route('dashboard', { period: p }), {}, { preserveState: true, preserveScroll: true })
}
const period = computed(() => props.period || 'Mois')
const periods = ['Semaine', 'Mois', 'Trimestre']

const kpiCards = [
    { label: 'Total', value: props.stats.total, color: '#63caff' },
    { label: 'Ouverts', value: props.stats.ouvert, color: '#63caff' },
    { label: 'En cours', value: props.stats.en_cours, color: '#ffc168' },
    { label: 'Résolus', value: props.stats.resolu, color: '#46f1bd' },
    { label: 'Fermés', value: props.stats.ferme, color: '#9eb6cd' },
]

const categoryColors = { 'Réseau':'#21baff', 'Matériel':'#31e0a9', 'Logiciel':'#ffc149', 'Électrique':'#ff6f84', 'Autre':'#9aa8ba' }

const catData = computed(() => ({
    labels: (props.ticketsParCategorie || []).map(c => c.categorie || 'Autre'),
    datasets: [{
        label: 'Tickets',
        data: (props.ticketsParCategorie || []).map(c => c.total || 0),
        backgroundColor: (props.ticketsParCategorie || []).map(c => categoryColors[c.categorie] || '#9aa8ba'),
        borderRadius: 8,
    }]
}))

const priData = computed(() => {
    const items = props.ticketsParPriorite || []
    return {
        labels: items.map(p => p.priorite),
        datasets: [{
            data: items.map(p => p.total || 0),
            backgroundColor: ['#ff3b5b', '#ff8d2a', '#ffcc3a', '#31dd92'],
            borderWidth: 0,
        }]
    }
})

const lineData = computed(() => ({
    labels: props.periodLabel || [],
    datasets: [{
        label: 'Tickets/jour',
        data: props.lineChartData || [],
        borderColor: '#2fcaff',
        backgroundColor: 'rgba(47,202,255,.15)',
        fill: true,
        tension: 0.38,
        pointRadius: 0,
    }]
}))

const chartOpts = {
    responsive: true,
    maintainAspectRatio: true,
    plugins: { legend: { labels: { color: '#7f9dbc', font: { family: 'Inter' }, boxWidth: 12 } } },
    scales: {
        x: { ticks: { color: '#7f9dbc' }, grid: { color: 'rgba(80,110,140,.15)' } },
        y: { ticks: { color: '#7f9dbc' }, grid: { color: 'rgba(80,110,140,.15)' } },
    }
}

const donutOpts = {
    responsive: true,
    plugins: { legend: { position: 'bottom', labels: { color: '#a8bfd5', font: { family: 'Inter', size: 11 }, padding: 16, boxWidth: 10 } } },
    cutout: '60%',
}

const statusBadgeClass = (status) => ({
    'Ouvert':'badge-ouvert', 'En cours':'badge-en-cours', 'Rapport en rédaction':'badge-rapport', 'Résolu':'badge-resolu', 'Fermé':'badge-ferme',
}[status] || 'badge-ferme')

const priorityBadgeClass = (p) => ({
    'Critique':'badge-critique', 'Haute':'badge-haute', 'Moyenne':'badge-moyenne', 'Basse':'badge-basse',
}[p] || 'badge-basse')

function starsFor(avg) {
    const s = Math.round(parseFloat(avg) || 0)
    return s > 5 ? 5 : s
}
function fmtDate(d) {
    if (!d) return ''
    const dt = new Date(d)
    return dt.toLocaleString('fr-FR', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' })
}
</script>

<template>
    <Head title="Tableau de bord Admin" />
    <AppLayout>
        <div class="space-y-7">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <div class="text-[10px] uppercase tracking-[0.2em] text-[#FF9800] font-semibold mb-2 section-title">ADMIN &bull; SOC SUPERVISION</div>
                    <h2 class="section-title text-[28px] text-[#dde7f6]">Vue d'ensemble ATECH</h2>
                </div>
                <div class="flex gap-2">
                    <button v-for="p in periods" :key="p" @click="changePeriod(p)"
                        class="rounded-xl px-4 py-[7px] text-[13px] font-semibold transition-colors border"
                        :class="period === p ? 'bg-[#0b3b6c] text-[#e4f8ff] border-[#2b8ed3]' : 'bg-transparent text-[#7f9dbc] border-[#1d3658] hover:border-[#2b8ed3]'">
                        {{ p }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <div v-for="kpi in kpiCards" :key="kpi.label" class="cyber-card rounded-[20px] p-5 flex flex-col gap-2">
                    <span class="text-[11px] text-[#7f9dbc] uppercase tracking-wider font-semibold">{{ kpi.label }}</span>
                    <span class="text-[28px] section-title font-800 leading-none" :style="{ color: kpi.color }">{{ kpi.value }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="cyber-card rounded-[20px] p-6 lg:col-span-2">
                    <h4 class="section-title text-[16px] text-[#dde7f6] mb-5">Tickets par catégorie</h4>
                    <Bar v-if="catData.labels.length" :data="catData" :options="chartOpts" />
                    <p v-else class="text-[14px] text-[#556e8c] py-8 text-center">Aucune donnée.</p>
                </div>
                <div class="cyber-card rounded-[20px] p-6">
                    <h4 class="section-title text-[16px] text-[#dde7f6] mb-5">Répartition priorités</h4>
                    <Doughnut v-if="priData.labels.length" :data="priData" :options="donutOpts" />
                    <p v-else class="text-[14px] text-[#556e8c] py-8 text-center">Aucune donnée.</p>
                </div>
            </div>

            <div class="cyber-card rounded-[20px] p-6">
                <h4 class="section-title text-[16px] text-[#dde7f6] mb-5">Évolution — {{ period }}</h4>
                <Line :data="lineData" :options="chartOpts" />
            </div>

            <div class="cyber-card rounded-[20px] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#1c3b5b]"><h4 class="section-title text-[16px] text-[#dde7f6]">Tickets récents</h4></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-[13px]">
                        <thead><tr class="border-b border-[#1c3b5b] bg-[#0d1e31] text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold">
                            <th class="px-5 py-3 text-left">Réf</th><th class="px-5 py-3 text-left">Titre</th><th class="px-5 py-3 text-left">Client</th><th class="px-5 py-3 text-left">Technicien</th><th class="px-5 py-3 text-left">Statut</th><th class="px-5 py-3 text-left">Priorité</th><th class="px-5 py-3 text-left">Date</th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="t in ticketsRecents" :key="t.id" @click="router.get(route('tickets.show', t.id))" class="border-t border-[#1c3b5b] cursor-pointer hover:bg-[#0f2438] transition-colors text-[#dde7f6]">
                                <td class="px-5 py-3.5 mono text-[12px] text-[#69b9ff] font-semibold">{{ t.ref }}</td>
                                <td class="px-5 py-3.5 font-medium">{{ t.titre }}</td>
                                <td class="px-5 py-3.5 text-[#a7bed8]">{{ t.client?.name || '—' }}</td>
                                <td class="px-5 py-3.5 text-[#a7bed8]">{{ t.technicien?.name || '—' }}</td>
                                <td class="px-5 py-3.5"><span :class="statusBadgeClass(t.statut)">{{ t.statut }}</span></td>
                                <td class="px-5 py-3.5"><span :class="priorityBadgeClass(t.priorite)">{{ t.priorite }}</span></td>
                                <td class="px-5 py-3.5 text-[#556e8c] text-[12px] whitespace-nowrap">{{ fmtDate(t.created_at) }}</td>
                            </tr>
                            <tr v-if="ticketsRecents.length===0"><td colspan="7" class="px-5 py-12 text-center text-[#556e8c]">Aucun ticket récent.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cyber-card rounded-[20px] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#1c3b5b]"><h4 class="section-title text-[16px] text-[#dde7f6]">Performance techniciens</h4></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-[13px]">
                        <thead><tr class="border-b border-[#1c3b5b] bg-[#0d1e31] text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold">
                            <th class="px-5 py-3 text-left">Technicien</th><th class="px-5 py-3 text-left">Spécialité</th><th class="px-5 py-3 text-left">Traités</th><th class="px-5 py-3 text-left">Note</th><th class="px-5 py-3 text-left">Charge</th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="t in performanceTechniciens" :key="t.id" class="border-t border-[#1c3b5b] hover:bg-[#0f2438] text-[#dde7f6]">
                                <td class="px-5 py-3.5 font-semibold">{{ t.name }}</td>
                                <td class="px-5 py-3.5 text-[#a7bed8]">{{ t.specialite || '—' }}</td>
                                <td class="px-5 py-3.5 font-medium">{{ t.total || t.tickets_count || 0 }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="text-[#ffcc3a]">{{ '★'.repeat(starsFor(t.moyenne || 0)) }}</span>
                                    <span class="text-[#2a3a55]">{{ '★'.repeat(5 - starsFor(t.moyenne || 0)) }}</span>
                                    <span class="ml-1 text-[12px] text-[#a7bed8]">{{ parseFloat(t.moyenne || 0).toFixed(1) }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-[#a7bed8]">{{ t.total || t.tickets_count || 0 }} ticket{{ (t.total || t.tickets_count || 0) > 1 ? 's' : '' }}</td>
                            </tr>
                            <tr v-if="performanceTechniciens.length===0"><td colspan="5" class="px-5 py-12 text-center text-[#556e8c]">Aucune donnée.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    tickets: { type: Array, required: true },
    filters: { type: Object, default: () => ({ search: '', statut: '', priorite: '' }) },
    techniciens: { type: Array, default: () => [] },
})

const search = ref(props.filters.search || '')
const statutFilter = ref(props.filters.statut || '')
const prioriteFilter = ref(props.filters.priorite || '')
const categoryFilter = ref(props.filters.categorie || '')

const statuses = ['', 'Ouvert', 'En cours', 'Rapport en rédaction', 'Résolu', 'Fermé']
const priorities = ['', 'Critique', 'Haute', 'Moyenne', 'Basse']
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

function applyFilters() {
    const params = {
        search: search.value,
        statut: statutFilter.value,
        priorite: prioriteFilter.value,
        categorie: categoryFilter.value,
    }
    router.get(route('tickets.index'), params, { preserveState: true, preserveScroll: true })
}

function clearFilters() {
    search.value = ''
    statutFilter.value = ''
    prioriteFilter.value = ''
    categoryFilter.value = ''
    applyFilters()
}

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

const hasActiveFilters = computed(() => {
    return statutFilter.value || prioriteFilter.value || categoryFilter.value
})
</script>

<template>
    <Head title="Tickets" />

    <AppLayout>
        <div class="space-y-7">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5">
                <div>
                    <div class="text-[11px] uppercase tracking-[0.2em] text-[#69b9ff] font-semibold mb-1.5">Tickets</div>
                    <h2 class="section-title text-[28px] text-main leading-tight">Liste des interventions</h2>
                    <p class="text-muted text-[13px] mt-1.5 font-medium">{{ props.tickets.length }} ticket(s) trouvé(s)</p>
                </div>
                <Link
                    :href="route('tickets.create')"
                    class="btn-cyber"
                >
                    <svg class="w-[15px] h-[15px] mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    + Nouveau ticket
                </Link>
            </div>

            <div class="cyber-card rounded-[20px] p-5">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[200px] max-w-[320px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-[15px] h-[15px] text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input
                            v-model="search"
                            type="text"
                            class="field !py-[8px] !pl-9 !w-full !text-[13px]"
                            placeholder="Rechercher par référence ou titre..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <select v-model="statutFilter" @change="applyFilters" class="field !py-[8px] !w-auto !text-[13px]">
                        <option value="">Tous les statuts</option>
                        <option v-for="s in statuses.slice(1)" :key="s" :value="s">{{ s }}</option>
                    </select>
                    <select v-model="prioriteFilter" @change="applyFilters" class="field !py-[8px] !w-auto !text-[13px]">
                        <option value="">Toutes priorités</option>
                        <option v-for="p in priorities.slice(1)" :key="p" :value="p">{{ p }}</option>
                    </select>
                    <select v-model="categoryFilter" @change="applyFilters" class="field !py-[8px] !w-auto !text-[13px]">
                        <option value="">Toutes catégories</option>
                        <option v-for="c in categories.slice(1)" :key="c" :value="c">{{ c }}</option>
                    </select>
                    <button
                        v-if="hasActiveFilters"
                        @click="clearFilters"
                        class="text-[12px] text-[#ff5c7a] hover:text-[#ff7d95] font-medium transition-colors duration-200"
                    >
                        Effacer filtres
                    </button>
                </div>
            </div>

            <div v-if="props.tickets.length === 0" class="text-center py-16 text-muted text-[14px]">
                Aucun ticket trouvé.
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    v-for="ticket in props.tickets"
                    :key="ticket.id"
                    @click="router.get(route('tickets.show', ticket.id))"
                    class="cyber-card rounded-[16px] p-5 hover-lift cursor-pointer"
                >
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <span class="mono text-[12px] text-[#69b9ff] font-semibold tracking-wider">{{ ticket.ref }}</span>
                        <span :class="priorityBadgeClass(ticket.priorite)">{{ ticket.priorite }}</span>
                    </div>
                    <h4 class="text-[15px] font-semibold text-main leading-tight mb-2">{{ ticket.titre }}</h4>
                    <p class="text-[13px] text-muted leading-relaxed line-clamp-2 mb-4">
                        {{ ticket.description }}
                    </p>
                    <div class="flex flex-wrap items-center gap-3 text-[11px] text-muted">
                        <span :class="statusBadgeClass(ticket.statut)">{{ ticket.statut }}</span>
                        <span class="text-muted">&bull;</span>
                        <span class="text-main font-medium">{{ ticket.client?.name || '—' }}</span>
                        <span class="text-muted">&bull;</span>
                        <span class="text-[#69b9ff]">{{ ticket.technicien?.name || 'Non assigné' }}</span>
                        <span class="text-muted">&bull;</span>
                        <span>{{ timeAgo(ticket.created_at) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

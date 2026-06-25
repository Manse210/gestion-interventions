<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    tickets: { type: Array, required: true },
    techniciens: { type: Array, default: () => [] },
})

const expandedTicket = ref(null)
const statutFilter = ref('')
const prioriteFilter = ref('')
const categorieFilter = ref('')
const technicienFilter = ref('')
const selectedTechniciens = ref({})
const showReassignModal = ref(false)
const reassignTicketId = ref(null)
const reassignTechId = ref('')

const statuses = ['', 'Ouvert', 'En cours', 'Rapport en rédaction', 'Résolu', 'Fermé']
const priorities = ['', 'Critique', 'Haute', 'Moyenne', 'Basse']
const categories = ['', 'Réseau', 'Matériel', 'Logiciel', 'Électrique', 'Autre']

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
    return props.tickets.filter(t => {
        if (statutFilter.value && t.statut !== statutFilter.value) return false
        if (prioriteFilter.value && t.priorite !== prioriteFilter.value) return false
        if (categorieFilter.value && t.categorie !== categorieFilter.value) return false
        if (technicienFilter.value && t.technicien?.id?.toString() !== technicienFilter.value) return false
        return true
    })
})

function toggleExpand(ticket) {
    expandedTicket.value = expandedTicket.value?.id === ticket.id ? null : ticket
    if (!selectedTechniciens.value[ticket.id]) {
        selectedTechniciens.value[ticket.id] = ticket.technicien?.id?.toString() || ''
    }
}

function assignTechnician(ticket) {
    if (!selectedTechniciens.value[ticket.id]) return
    router.post(route('tickets.assign', ticket.id), {
        technicien_id: selectedTechniciens.value[ticket.id],
    }, { preserveState: true, preserveScroll: true })
}

function openReassignModal(ticket) {
    reassignTicketId.value = ticket.id
    reassignTechId.value = ticket.technicien?.id?.toString() || ''
    showReassignModal.value = true
}

function closeReassignModal() {
    showReassignModal.value = false
    reassignTicketId.value = null
    reassignTechId.value = ''
}

function submitReassign() {
    if (!reassignTechId.value || !reassignTicketId.value) return
    router.post(route('tickets.assign', reassignTicketId.value), {
        technicien_id: reassignTechId.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => { closeReassignModal() },
    })
}

function updateStatus(ticket, statut) {
    router.patch(route('tickets.status', ticket.id), { statut }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => { expandedTicket.value = null },
    })
}

function exportCSV() {
    const headers = ['Réf', 'Titre', 'Client', 'Technicien', 'Statut', 'Priorité', 'Catégorie', 'Date']
    const rows = filteredTickets.value.map(t => [
        t.ref, t.titre, t.client?.name || '', t.technicien?.name || '', t.statut, t.priorite, t.categorie || '', t.created_at
    ])
    const csv = [headers.join(','), ...rows.map(r => r.map(v => `"${v || ''}"`).join(','))].join('\n')
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'tickets-admin.csv'
    a.click()
    URL.revokeObjectURL(url)
}
</script>

<template>
    <Head title="Gestion des tickets" />

    <AppLayout>
        <div class="space-y-7">
            <h2 class="section-title text-[26px] text-[#dde7f6]">Gestion de tous les tickets</h2>

            <div class="cyber-card rounded-[20px] p-4">
                <div class="flex flex-wrap items-center gap-3">
                    <select v-model="statutFilter" class="field !py-[8px] !w-auto text-[13px]">
                        <option value="">Tous les statuts</option>
                        <option v-for="s in statuses.slice(1)" :key="s" :value="s">{{ s }}</option>
                    </select>
                    <select v-model="prioriteFilter" class="field !py-[8px] !w-auto text-[13px]">
                        <option value="">Toutes priorités</option>
                        <option v-for="p in priorities.slice(1)" :key="p" :value="p">{{ p }}</option>
                    </select>
                    <select v-model="categorieFilter" class="field !py-[8px] !w-auto text-[13px]">
                        <option value="">Toutes catégories</option>
                        <option v-for="c in categories.slice(1)" :key="c" :value="c">{{ c }}</option>
                    </select>
                    <select v-model="technicienFilter" class="field !py-[8px] !w-auto text-[13px]">
                        <option value="">Tous les techniciens</option>
                        <option v-for="tech in techniciens" :key="tech.id" :value="tech.id.toString()">{{ tech.name }}</option>
                    </select>
                    <button
                        @click="exportCSV"
                        class="rounded-xl border border-[#1d3658] bg-[#0f1f35] text-[#a7bed8] px-4 py-[7px] text-[12px] font-semibold hover:border-[#2ea8ff] hover:text-[#dde7f6] transition-colors duration-200 ml-auto"
                    >
                        <svg class="w-[14px] h-[14px] inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Exporter CSV
                    </button>
                    <span class="text-[12px] text-[#556e8c] font-medium ml-1">
                        {{ filteredTickets.length }} résultat{{ filteredTickets.length !== 1 ? 's' : '' }}
                    </span>
                </div>
            </div>

            <div class="cyber-card rounded-[20px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-[#dde7f6] text-[13px]">
                        <thead>
                            <tr class="border-b border-[#1c3b5b] bg-[#0d1e31] text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold">
                                <th class="px-5 py-3 text-left">Réf</th>
                                <th class="px-5 py-3 text-left">Titre</th>
                                <th class="px-5 py-3 text-left">Statut</th>
                                <th class="px-5 py-3 text-left">Priorité</th>
                                <th class="px-5 py-3 text-left">Catégorie</th>
                                <th class="px-5 py-3 text-left">Technicien</th>
                                <th class="px-5 py-3 text-left">Date</th>
                                <th class="px-5 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="ticket in filteredTickets" :key="ticket.id">
                                <tr
                                    @click="toggleExpand(ticket)"
                                    class="border-t border-[#1c3b5b] cursor-pointer hover:bg-[#0f2438] transition-colors"
                                >
                                    <td class="px-5 py-3.5 mono text-[12px] text-[#69b9ff] font-semibold">{{ ticket.ref }}</td>
                                    <td class="px-5 py-3.5 font-medium text-[#dde7f6]">{{ ticket.titre }}</td>
                                    <td class="px-5 py-3.5">
                                        <span :class="statusBadgeClass(ticket.statut)">{{ ticket.statut }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span :class="priorityBadgeClass(ticket.priorite)">{{ ticket.priorite }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-[#a7bed8]">{{ ticket.categorie || '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <span v-if="ticket.technicien" class="text-[#a7bed8]">{{ ticket.technicien.name }}</span>
                                        <span v-else class="text-[#ff5b6a] font-bold">&mdash; à affecter</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-[#556e8c] text-[12px] whitespace-nowrap">{{ ticket.created_at }}</td>
                                    <td class="px-5 py-3.5">
                                        <button
                                            @click.stop="openReassignModal(ticket)"
                                            class="rounded-lg border border-[#1d3658] bg-transparent text-[#7f9dbc] px-3 py-[5px] text-[11px] font-semibold hover:border-[#2ea8ff] hover:text-[#dde7f6] transition-colors duration-200"
                                        >
                                            Réaffecter
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="expandedTicket?.id === ticket.id">
                                    <td colspan="8" class="px-5 py-5 bg-[#0a1322] border-t border-[#1c3b5b]">
                                        <div class="space-y-5">
                                            <div>
                                                <h4 class="text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold mb-2">Description</h4>
                                                <p class="text-[13px] text-[#a7bed8] whitespace-pre-wrap">{{ ticket.description || 'Aucune description.' }}</p>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-3">
                                                <span class="text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold">Status :</span>
                                                <span :class="statusBadgeClass(ticket.statut)">{{ ticket.statut }}</span>
                                                <span class="text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold ml-4">Priorité :</span>
                                                <span :class="priorityBadgeClass(ticket.priorite)">{{ ticket.priorite }}</span>
                                                <span class="text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold ml-4">Catégorie :</span>
                                                <span class="text-[13px] text-[#a7bed8]">{{ ticket.categorie || '—' }}</span>
                                            </div>

                                            <div>
                                                <h4 class="text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold mb-3">Assignation</h4>
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <select v-model="selectedTechniciens[ticket.id]" class="field !py-[8px] !w-auto text-[13px]">
                                                        <option value="">Non assigné</option>
                                                        <option v-for="tech in techniciens" :key="tech.id" :value="tech.id.toString()">{{ tech.name }}</option>
                                                    </select>
                                                    <button
                                                        @click="assignTechnician(ticket)"
                                                        :disabled="!selectedTechniciens[ticket.id]"
                                                        class="btn-cyber !py-[8px] !text-[12px]"
                                                        :class="{ 'opacity-50 pointer-events-none': !selectedTechniciens[ticket.id] }"
                                                    >
                                                        Assigner
                                                    </button>
                                                </div>
                                            </div>

                                            <div>
                                                <h4 class="text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold mb-3">Changer statut</h4>
                                                <div class="flex flex-wrap gap-2">
                                                    <button
                                                        v-for="s in statuses.slice(1)"
                                                        :key="s"
                                                        @click="updateStatus(ticket, s)"
                                                        class="rounded-xl border px-3 py-[6px] text-[12px] font-semibold transition-colors duration-200"
                                                        :class="ticket.statut === s
                                                            ? 'border-[#2ea8ff] bg-[#2ea8ff]/15 text-[#2ea8ff]'
                                                            : 'border-[#1d3658] bg-transparent text-[#7f9dbc] hover:border-[#2ea8ff] hover:text-[#dde7f6]'"
                                                    >
                                                        {{ s }}
                                                    </button>
                                                </div>
                                            </div>

                                            <div v-if="ticket.history && ticket.history.length > 0">
                                                <h4 class="text-[11px] uppercase tracking-wider text-[#7f9dbc] font-semibold mb-3">Historique</h4>
                                                <div class="space-y-0">
                                                    <div
                                                        v-for="(entry, i) in ticket.history"
                                                        :key="entry.id"
                                                        class="relative pl-6 pb-4 last:pb-0"
                                                        :class="i < ticket.history.length - 1 ? 'border-l-2 border-[#1d3658]' : ''"
                                                    >
                                                        <div class="absolute left-[-5px] top-0 w-[8px] h-[8px] rounded-full bg-[#1d82ff] border-2 border-[#0b1627]" />
                                                        <div>
                                                            <p class="text-[12px] text-[#dde7f6] font-medium">{{ entry.action }}</p>
                                                            <p class="text-[11px] text-[#7d95b6] mt-0.5">
                                                                Par <span class="text-[#69b9ff] font-medium">{{ entry.user?.name }}</span>
                                                                &mdash;
                                                                <span class="text-[#556e8c]">{{ entry.created_at }}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="filteredTickets.length === 0">
                                <td colspan="8" class="px-5 py-12 text-center text-[#556e8c]">Aucun ticket trouvé.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Teleport to="body">
                <div
                    v-if="showReassignModal"
                    class="fixed inset-0 z-50 flex items-center justify-center"
                >
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeReassignModal" />
                    <div class="relative cyber-card rounded-[20px] p-6 w-full max-w-md mx-4">
                        <h4 class="section-title text-[17px] text-[#dde7f6] mb-5">Réaffecter le ticket</h4>
                        <div class="space-y-3 mb-5">
                            <div
                                v-for="tech in techniciens"
                                :key="tech.id"
                                @click="reassignTechId = tech.id.toString()"
                                class="flex items-center justify-between p-3 rounded-xl cursor-pointer transition-colors duration-200"
                                :class="reassignTechId === tech.id.toString()
                                    ? 'bg-[#0b3b6c] border border-[#2b8ed3]'
                                    : 'bg-transparent border border-[#1d3658] hover:border-[#2b8ed3]'"
                            >
                                <div>
                                    <p class="text-[13px] font-semibold text-[#dde7f6]">{{ tech.name }}</p>
                                    <p class="text-[11px] text-[#7f9dbc]">{{ tech.specialite || '—' }} &bull; Charge : {{ tech.charge || tech.tickets_count || 0 }}</p>
                                </div>
                                <button
                                    v-if="reassignTechId === tech.id.toString()"
                                    class="btn-cyber !py-[6px] !px-[14px] !text-[11px]"
                                >
                                    Assigner
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button
                                @click="closeReassignModal"
                                class="rounded-xl border border-[#1d3658] bg-transparent text-[#a7bed8] px-4 py-[8px] text-[13px] font-semibold hover:border-[#3d6392] hover:text-[#dde7f6] transition-colors duration-200"
                            >
                                Annuler
                            </button>
                            <button
                                @click="submitReassign"
                                :disabled="!reassignTechId"
                                class="btn-cyber"
                                :class="{ 'opacity-50 pointer-events-none': !reassignTechId }"
                            >
                                Confirmer
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </AppLayout>
</template>

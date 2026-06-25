<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    ticket: { type: Object, required: true },
})

const form = ref({
    travaux: '',
    duree_heures: 0,
    duree_minutes: 0,
    materiel: '',
    observations: '',
    recommandations: '',
})

const processing = ref(false)
const errors = ref({})

const submitDisabled = computed(() => {
    return (form.value.travaux || '').length <= 20
        || (form.value.duree_heures < 0 && form.value.duree_minutes < 0)
        || (form.value.duree_minutes < 0 || form.value.duree_minutes > 59)
})

function submit() {
    processing.value = true
    errors.value = {}
    router.post(route('reports.store', props.ticket.id), form.value, {
        preserveScroll: true,
        onFinish: () => { processing.value = false },
        onError: (errs) => { errors.value = errs },
    })
}
</script>

<template>
    <Head title="Rapport d'intervention" />

    <AppLayout>
        <div class="max-w-3xl mx-auto space-y-7">
            <Link
                :href="route('tickets.show', ticket.id)"
                class="inline-flex items-center gap-2 text-muted hover:text-main text-[13px] font-medium transition-colors duration-200"
            >
                <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                &larr; retour ticket
            </Link>

            <div class="cyber-card rounded-[20px] p-6">
                <div class="text-[10px] uppercase tracking-[0.2em] text-[#6bcbff] font-semibold mb-2 font-['Space_Grotesk']">RAPPORT D'INTERVENTION</div>
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <span class="mono text-[13px] text-[#69b9ff] font-semibold tracking-wider">{{ ticket.ref }}</span>
                    <span class="text-[15px] font-semibold text-main">{{ ticket.titre }}</span>
                </div>
                <p class="text-[12px] text-muted">Client : {{ ticket.client?.name }} &bull; Technicien : {{ ticket.technicien?.name || 'Non assigné' }}</p>
            </div>

            <form @submit.prevent="submit" class="cyber-card rounded-[20px] p-6 space-y-6">
                <div>
                    <label for="travaux" class="label">Description des travaux</label>
                    <textarea
                        id="travaux"
                        v-model="form.travaux"
                        rows="6"
                        class="field w-full min-h-[120px] resize-y"
                        placeholder="Décrivez en détail les travaux réalisés (minimum 20 caractères)..."
                    ></textarea>
                    <p v-if="errors.travaux" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.travaux }}</p>
                    <p class="text-[11px] text-muted mt-1.5">{{ (form.travaux || '').length }} / min 20 caractères</p>
                </div>

                <div>
                    <label class="label">Durée de l'intervention</label>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <input
                                v-model.number="form.duree_heures"
                                type="number"
                                min="0"
                                class="field w-[70px] text-center"
                                placeholder="0"
                            />
                            <span class="text-[12px] text-sub font-medium">h</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <input
                                v-model.number="form.duree_minutes"
                                type="number"
                                min="0"
                                max="59"
                                class="field w-[70px] text-center"
                                placeholder="0"
                            />
                            <span class="text-[12px] text-sub font-medium">min</span>
                        </div>
                    </div>
                    <p v-if="errors.duree_heures" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.duree_heures }}</p>
                    <p v-if="errors.duree_minutes" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.duree_minutes }}</p>
                    <p v-if="form.duree_minutes < 0 || form.duree_minutes > 59" class="text-[#ff5c7a] text-[11px] mt-1.5">Les minutes doivent être entre 0 et 59.</p>
                </div>

                <div>
                    <label for="materiel" class="label">Matériel utilisé</label>
                    <input
                        id="materiel"
                        v-model="form.materiel"
                        type="text"
                        class="field w-full"
                        placeholder="Listez le matériel utilisé..."
                    />
                    <p v-if="errors.materiel" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.materiel }}</p>
                </div>

                <div>
                    <label for="observations" class="label">Observations</label>
                    <textarea
                        id="observations"
                        v-model="form.observations"
                        rows="4"
                        class="field w-full resize-y"
                        placeholder="Observations complémentaires..."
                    ></textarea>
                    <p v-if="errors.observations" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.observations }}</p>
                </div>

                <div>
                    <label for="recommandations" class="label">Recommandations</label>
                    <textarea
                        id="recommandations"
                        v-model="form.recommandations"
                        rows="4"
                        class="field w-full resize-y"
                        placeholder="Recommandations pour le client..."
                    ></textarea>
                    <p v-if="errors.recommandations" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.recommandations }}</p>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-subtle">
                    <Link
                        :href="route('tickets.show', ticket.id)"
                        class="rounded-xl border border-subtle bg-transparent text-sub px-5 py-[10px] text-[13px] font-semibold hover:border-[#3d6392] hover:text-main transition-colors duration-200"
                    >
                        Annuler
                    </Link>
                    <button
                        type="submit"
                        :disabled="submitDisabled || processing"
                        class="btn-cyber"
                        :class="{ 'opacity-50 pointer-events-none': submitDisabled || processing }"
                    >
                        <svg v-if="processing" class="w-[15px] h-[15px] mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                        Soumettre le rapport &bull; Clôturer en Résolu
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

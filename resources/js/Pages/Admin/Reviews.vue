<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    evaluations: { type: Array, required: true },
    techniciens: { type: Array, default: () => [] },
})

const technicienFilter = ref('')

const filteredEvaluations = computed(() => {
    if (!technicienFilter.value) return props.evaluations
    return props.evaluations.filter(e => e.ticket?.technicien_id?.toString() === technicienFilter.value.toString())
})

const moyenneGlobale = computed(() => {
    if (!props.evaluations.length) return 0
    const sum = props.evaluations.reduce((a, e) => a + (e.note || 0), 0)
    return sum / props.evaluations.length
})

const filteredMoyenne = computed(() => {
    if (!filteredEvaluations.value.length) return 0
    const sum = filteredEvaluations.value.reduce((a, e) => a + (e.note || 0), 0)
    return sum / filteredEvaluations.value.length
})
</script>

<template>
    <Head title="Avis clients" />

    <AppLayout>
        <div class="space-y-7">
            <h2 class="section-title text-[26px] text-[#dde7f6]">Avis clients</h2>

            <div class="flex flex-wrap items-center gap-4">
                <div class="cyber-card rounded-[20px] p-5 flex items-center gap-4">
                    <div class="flex items-center gap-1">
                        <svg v-for="s in 5" :key="s" class="w-[22px] h-[22px]" :class="s <= Math.round(moyenneGlobale) ? 'text-[#ffcc3a]' : 'text-[#2a3a55]'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-[#7f9dbc] uppercase tracking-wider font-semibold">Note moyenne</p>
                        <p class="text-[22px] font-['Space_Grotesk'] font-bold text-[#dde7f6]">{{ moyenneGlobale.toFixed(1) }} <span class="text-[14px] text-[#7f9dbc]">/5</span></p>
                    </div>
                </div>

                <div class="flex-1" />

                <select v-model="technicienFilter" class="field !py-[8px] !w-auto text-[13px]">
                    <option value="">Tous les techniciens</option>
                    <option v-for="tech in techniciens" :key="tech.id" :value="tech.id.toString()">{{ tech.name }}</option>
                </select>
            </div>

            <p v-if="technicienFilter && filteredEvaluations.length" class="text-[12px] text-[#7f9dbc]">
                Note filtrée : <span class="font-bold text-[#ffcc3a]">{{ filteredMoyenne.toFixed(1) }}/5</span> &bull; {{ filteredEvaluations.length }} avis
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-for="evalItem in filteredEvaluations"
                    :key="evalItem.id"
                    class="cyber-card rounded-[18px] p-5 flex flex-col"
                >
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[14px] font-semibold text-[#dde7f6]">{{ evalItem.client?.name || 'Anonyme' }}</span>
                        <div class="flex items-center gap-0.5">
                            <svg v-for="s in 5" :key="s" class="w-[18px] h-[18px]" :class="s <= evalItem.note ? 'text-[#ffcc3a]' : 'text-[#2a3a55]'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        </div>
                    </div>

                    <p v-if="evalItem.commentaire" class="text-[13px] text-[#a7bed8] leading-relaxed flex-1">
                        &#8220;{{ evalItem.commentaire }}&#8221;
                    </p>
                    <p v-else class="text-[13px] text-[#556e8c] italic flex-1">
                        Aucun commentaire.
                    </p>

                    <div class="text-[11px] text-[#556e8c] mt-4 pt-3 border-t border-[#1c3b5b] flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span class="mono text-[#69b9ff] font-medium">{{ evalItem.ticket?.ref || '—' }}</span>
                        <span>&bull;</span>
                        <span>{{ evalItem.ticket?.technicien?.name || '—' }}</span>
                        <span>&bull;</span>
                        <span>{{ evalItem.created_at }}</span>
                    </div>
                </div>
            </div>

            <div
                v-if="filteredEvaluations.length === 0"
                class="cyber-card rounded-[20px] p-12 text-center"
            >
                <p class="text-[14px] text-[#556e8c]">Aucun avis trouvé.</p>
            </div>
        </div>
    </AppLayout>
</template>

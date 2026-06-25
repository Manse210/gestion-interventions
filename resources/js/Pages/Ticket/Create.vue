<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = ref({
    titre: '',
    description: '',
    priorite: 'Moyenne',
    categorie: 'Logiciel',
    piece_jointe: null,
})

const processing = ref(false)
const errors = ref({})

const pieceJointeName = ref('')

const canSubmit = computed(() => {
    return form.value.titre.trim().length >= 3 && form.value.description.trim().length >= 8
})

function handleFile(e) {
    const file = e.target.files[0]
    form.value.piece_jointe = file
    pieceJointeName.value = file ? file.name : ''
}

function submit() {
    if (!canSubmit.value) return
    processing.value = true
    errors.value = {}
    const data = new FormData()
    data.append('titre', form.value.titre)
    data.append('description', form.value.description)
    data.append('priorite', form.value.priorite)
    data.append('categorie', form.value.categorie)
    if (form.value.piece_jointe) {
        data.append('piece_jointe', form.value.piece_jointe)
    }
    router.post(route('tickets.store'), data, {
        preserveScroll: true,
        onSuccess: () => {
            form.value = { titre: '', description: '', priorite: 'Moyenne', categorie: 'Logiciel', piece_jointe: null }
            pieceJointeName.value = ''
        },
        onError: (errs) => {
            errors.value = errs
            processing.value = false
        },
        onFinish: () => {
            processing.value = false
        },
    })
}

const priorityBadgeClass = (priority) => ({
    'Critique': 'badge-critique',
    'Haute': 'badge-haute',
    'Moyenne': 'badge-moyenne',
    'Basse': 'badge-basse',
}[priority] || 'badge-basse')
</script>

<template>
    <Head title="Nouveau ticket" />

    <AppLayout>
        <div class="max-w-3xl mx-auto space-y-7">
            <div>
                <Link
                    :href="route('tickets.index')"
                    class="inline-flex items-center gap-2 text-[#7d95b6] hover:text-[#dcefff] text-[13px] font-medium transition-colors duration-200 mb-4"
                >
                    <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Retour aux tickets
                </Link>
                <div class="text-[11px] uppercase tracking-[0.2em] text-[#66b8ff] font-semibold mb-1.5">Nouveau ticket</div>
                <h2 class="section-title text-[26px] text-[#dcefff] leading-tight">Ouvrir une intervention ATECH</h2>
            </div>

            <form @submit.prevent="submit" class="cyber-card rounded-[20px] p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label for="titre" class="label">Titre de l'intervention <span class="text-[#ff5c7a]">*</span></label>
                        <input
                            id="titre"
                            v-model="form.titre"
                            type="text"
                            class="field w-full"
                            placeholder="Ex: Panne réseau étage 3"
                        />
                        <p v-if="errors.titre" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.titre }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="label">Description détaillée <span class="text-[#ff5c7a]">*</span></label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="5"
                            class="field w-full min-h-[138px] resize-y"
                            placeholder="Décrivez le problème rencontré, le contexte, les équipements impactés..."
                        ></textarea>
                        <p v-if="errors.description" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.description }}</p>
                    </div>

                    <div>
                        <label for="categorie" class="label">Catégorie</label>
                        <select id="categorie" v-model="form.categorie" class="field w-full">
                            <option value="Réseau">Réseau</option>
                            <option value="Logiciel">Logiciel</option>
                            <option value="Matériel">Matériel</option>
                            <option value="Électrique">Électrique</option>
                            <option value="Autre">Autre</option>
                        </select>
                        <p v-if="errors.categorie" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.categorie }}</p>
                    </div>

                    <div>
                        <label for="priorite" class="label">Priorité</label>
                        <select id="priorite" v-model="form.priorite" class="field w-full">
                            <option value="Critique">Critique</option>
                            <option value="Haute">Haute</option>
                            <option value="Moyenne">Moyenne</option>
                            <option value="Basse">Basse</option>
                        </select>
                        <span :class="priorityBadgeClass(form.priorite)" class="mt-2">{{ form.priorite }}</span>
                        <p v-if="errors.priorite" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.priorite }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="label">Pièce jointe <span class="text-[#556e8c] font-normal">(facultatif)</span></label>
                        <div class="relative">
                            <input
                                type="file"
                                @change="handleFile"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            />
                            <div class="field w-full flex items-center gap-3 cursor-pointer">
                                <svg class="w-[17px] h-[17px] text-[#556e8c] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                <span class="text-[13px] text-[#556e8c] truncate">{{ pieceJointeName || 'Cliquez pour sélectionner un fichier (PDF, image, ZIP...)' }}</span>
                            </div>
                        </div>
                        <p v-if="errors.piece_jointe" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.piece_jointe }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-2 border-t border-[#1c3b5b]">
                    <button
                        type="submit"
                        :disabled="!canSubmit || processing"
                        class="btn-cyber"
                        :class="{ 'opacity-50 pointer-events-none': !canSubmit || processing }"
                    >
                        <svg v-if="processing" class="w-[15px] h-[15px] mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                        Créer le ticket
                    </button>
                    <Link
                        :href="route('tickets.index')"
                        class="text-[13px] text-[#7d95b6] hover:text-[#dcefff] font-medium transition-colors duration-200"
                    >
                        Annuler
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

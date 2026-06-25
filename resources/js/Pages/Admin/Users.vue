<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    users: { type: Array, required: true },
})

const showModal = ref(false)
const form = ref({
    name: '',
    email: '',
    password: '',
    specialite: '',
})
const errors = ref({})
const processing = ref(false)

const specialites = ['Réseau', 'Logiciel', 'Matériel', 'Électrique', 'Autre']

function toggleUser(user) {
    router.post(route('admin.users.toggle', user.id), {}, {
        preserveState: true,
        preserveScroll: true,
    })
}

function openModal() {
    form.value = { name: '', email: '', password: '', specialite: '' }
    errors.value = {}
    showModal.value = true
}

function closeModal() {
    showModal.value = false
}

function submit() {
    processing.value = true
    errors.value = {}
    router.post(route('admin.users.technician'), form.value, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false
            processing.value = false
        },
        onError: (errs) => {
            errors.value = errs
            processing.value = false
        },
    })
}
</script>

<template>
    <Head title="Comptes & accès" />

    <AppLayout>
        <div class="space-y-7">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="section-title text-[26px] text-main">Comptes & accès</h2>
                <button @click="openModal" class="btn-cyber">
                    <svg class="w-[15px] h-[15px] mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    + Créer un compte technicien
                </button>
            </div>

            <div class="cyber-card rounded-[20px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-main text-[13px]">
                        <thead>
                            <tr class="border-b border-subtle bg-[#0d1e31] text-[11px] uppercase tracking-wider text-muted font-semibold">
                                <th class="px-5 py-3 text-left">Utilisateur</th>
                                <th class="px-5 py-3 text-left">Email</th>
                                <th class="px-5 py-3 text-left">Rôle</th>
                                <th class="px-5 py-3 text-left">Spécialité</th>
                                <th class="px-5 py-3 text-left">Statut</th>
                                <th class="px-5 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id" class="border-t border-subtle hover:bg-[#0f2438] transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-main">{{ user.name }}</td>
                                <td class="px-5 py-3.5 text-sub">{{ user.email }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="chip capitalize" :class="{
                                        'bg-[#1a1030] text-[#b68cff]': user.role === 'admin',
                                        'bg-[#0b1d35] text-[#63caff]': user.role === 'technicien',
                                        'bg-[#0d2220] text-[#31e0a9]': user.role === 'client',
                                    }">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sub">{{ user.specialite || '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="chip" :class="user.actif
                                        ? 'bg-[#0d2b22] text-[#5cffbf]'
                                        : 'bg-[#2a1a1a] text-[#ff6f84]'">
                                        {{ user.actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <button
                                        @click="toggleUser(user)"
                                        class="text-[12px] font-semibold transition-colors duration-200"
                                        :class="user.actif
                                            ? 'text-[#ff6f84] hover:text-[#ff3b5b]'
                                            : 'text-[#31e0a9] hover:text-[#24e0b1]'"
                                    >
                                        {{ user.actif ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="6" class="px-5 py-12 text-center text-muted">Aucun utilisateur.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center"
            >
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal" />
                <div class="relative cyber-card rounded-[20px] p-6 w-full max-w-md mx-4">
                    <h4 class="section-title text-[17px] text-main mb-5">Créer un compte technicien</h4>

                    <div class="space-y-4">
                        <div>
                            <label for="name" class="label">Nom complet</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="field w-full"
                                placeholder="Nom du technicien"
                                autocomplete="off"
                            />
                            <p v-if="errors.name" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.name }}</p>
                        </div>

                        <div>
                            <label for="email" class="label">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="field w-full"
                                placeholder="email@atech.dz"
                                autocomplete="off"
                            />
                            <p v-if="errors.email" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.email }}</p>
                        </div>

                        <div>
                            <label for="password" class="label">Mot de passe</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="field w-full"
                                placeholder="Mot de passe sécurisé"
                                autocomplete="new-password"
                            />
                            <p v-if="errors.password" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.password }}</p>
                        </div>

                        <div>
                            <label for="specialite" class="label">Spécialité</label>
                            <select id="specialite" v-model="form.specialite" class="field w-full">
                                <option value="" disabled>Sélectionner une spécialité</option>
                                <option v-for="s in specialites" :key="s" :value="s">{{ s }}</option>
                            </select>
                            <p v-if="errors.specialite" class="text-[#ff5c7a] text-[12px] mt-1.5">{{ errors.specialite }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-subtle">
                        <button
                            @click="closeModal"
                            class="rounded-xl border border-subtle bg-transparent text-sub px-5 py-[9px] text-[13px] font-semibold hover:border-[#3d6392] hover:text-main transition-colors duration-200"
                        >
                            Annuler
                        </button>
                        <button
                            @click="submit"
                            :disabled="processing"
                            class="btn-cyber"
                            :class="{ 'opacity-50 pointer-events-none': processing }"
                        >
                            <svg v-if="processing" class="w-[15px] h-[15px] mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                            Créer
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

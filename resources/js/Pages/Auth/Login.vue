<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: 'lea.morel@client.fr',
    password: 'client123',
    role: 'Client',
});

const roles = ['Client', 'Technicien', 'Admin'];

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion" />

    <div class="flex min-h-screen items-center justify-center bg-[#060b14] px-4 py-10 g-grid">
        <div class="w-full max-w-[480px] ml-auto mr-4 lg:mr-20">
            <div class="cyber-card rounded-[26px] p-7 sm:p-9">
                <p class="text-[11px] mono tracking-[0.22em] text-[#57c8ff] uppercase mb-1">
                    ATECH PORTAL ACCESS
                </p>

                <h1 class="section-title text-[26px] text-main leading-tight mb-1">
                    Bienvenue dans votre espace
                </h1>

                <p class="text-sm text-sub mb-7">
                    Connectez-vous pour accéder à la plateforme de gestion des interventions.
                </p>

                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label for="email" class="label">Nom d'utilisateur</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="field w-full"
                            required
                            autocomplete="username"
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-[#ff8b95]">{{ form.errors.email }}</p>
                    </div>

                    <div class="mb-5">
                        <label for="password" class="label">Mot de passe</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="field w-full"
                            required
                            autocomplete="current-password"
                        />
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-[#ff8b95]">{{ form.errors.password }}</p>
                    </div>

                    <div class="mb-5">
                        <p class="label">Rôle</p>
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                v-for="role in roles"
                                :key="role"
                                type="button"
                                class="rounded-xl border py-2.5 text-[13px] font-semibold transition-all duration-200"
                                :class="form.role === role
                                    ? 'border-[#43c9ff] bg-[#0f2b45] text-main'
                                    : 'border-subtle text-muted bg-transparent hover:border-subtle'"
                                @click="form.role = role"
                            >
                                {{ role }}
                            </button>
                        </div>
                        <input type="hidden" v-model="form.role" />
                    </div>

                    <div v-if="form.errors.role" class="mb-4 text-xs text-[#ff8b95]">
                        {{ form.errors.role }}
                    </div>

                    <button
                        type="submit"
                        class="btn-cyber w-full py-3 text-[15px]"
                        :class="{ 'opacity-50 pointer-events-none': form.processing }"
                        :disabled="form.processing"
                    >
                        Se connecter &rarr;
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-muted">
                    Pas encore de compte ?
                    <Link :href="route('register')" class="text-[#43c9ff] hover:underline font-semibold">
                        Créer un compte
                    </Link>
                </p>

                <div class="mt-7 pt-5 border-t border-subtle">
                    <p class="text-[11px] mono text-muted uppercase tracking-wider mb-3">Comptes de démonstration</p>
                    <div class="space-y-2 mono text-xs text-muted">
                        <div class="flex justify-between">
                            <span class="text-sub">Client</span>
                            <span>lea.morel@client.fr / client123</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sub">Technicien</span>
                            <span>tech@atech.fr / technicien123</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sub">Admin</span>
                            <span>admin@atech.fr / admin123</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

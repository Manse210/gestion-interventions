<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="ATECH Cybersécurité" />

    <div class="relative min-h-screen bg-[#060b14] text-[#dbeaff] overflow-hidden g-grid">
        <div class="absolute -top-24 right-1/4 w-[500px] h-[500px] rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(0,195,255,0.12) 0%, transparent 70%);" />
        <div class="absolute bottom-[-120px] left-10 w-[500px] h-[500px] rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(36,226,179,0.08) 0%, transparent 70%);" />

        <div class="absolute top-0 inset-x-0 z-30 border-b border-[#1c3b5b]/50 glass">
            <div class="mx-auto max-w-7xl flex items-center justify-between px-6 py-3.5">
                <div class="flex items-center gap-3">
                    <ApplicationLogo class="h-9 w-9 text-[#43c9ff]" />
                    <span class="section-title text-lg text-[#dcefff] tracking-tight">ATECH</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="rounded-xl border px-4 py-2 text-[13px] font-semibold transition-all duration-200"
                        :class="route().current('login')
                            ? 'border-[#43c9ff] bg-[#0f2b45] text-[#b8eaff]'
                            : 'border-[#1f3b59] text-[#a7bed8] hover:border-[#22425e] bg-transparent'"
                    >
                        Se connecter
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="btn-cyber text-[13px] px-4 py-2"
                        :class="{ 'border border-[#1f3b59] bg-transparent text-[#a7bed8] hover:border-[#22425e]': route().current('register') }"
                    >
                        S'inscrire
                    </Link>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-6 pt-28 pb-16 grid grid-cols-1 lg:grid-cols-[1.05fr_0.95fr] gap-10 items-center">
            <div class="hidden lg:block">
                <div class="flex items-center gap-3 mb-5">
                    <ApplicationLogo class="h-11 w-11 text-[#43c9ff]" />
                    <div class="leading-tight">
                        <p class="section-title text-xl text-[#dcefff]">ATECH Cybersécurité</p>
                        <p class="text-[11px] mono tracking-[0.16em] text-[#57c8ff] uppercase">SOC &bull; Intervention &bull; Supervision</p>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 rounded-full border border-[#1e3b5b] bg-[#0f2b45]/60 px-3 py-1.5 mb-6">
                    <span class="h-2 w-2 rounded-full bg-[#24e2b3] animate-pulse" />
                    <span class="text-[11px] mono text-[#57c8ff] tracking-wider uppercase">Prototype opérationnel</span>
                </div>

                <h1 class="section-title text-[34px] leading-[1.15] text-[#dcefff] mb-5">
                    Pilotage des interventions techniques, en un seul espace.
                </h1>

                <p class="text-[15px] text-[#90aac8] leading-relaxed mb-8 max-w-lg">
                    Centralisez vos tickets d'intervention, suivez l'avancement en temps réel
                    et collaborez avec vos techniciens depuis une interface conçue pour les
                    équipes de cybersécurité.
                </p>

                <div class="grid grid-cols-2 gap-4">
                    <div class="cyber-card rounded-[20px] p-5">
                        <div class="flex items-center gap-2.5 mb-2">
                            <svg class="h-5 w-5 text-[#43c9ff]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="font-bold text-[#dcefff] text-sm">Gestion des tickets</span>
                        </div>
                        <p class="text-[13px] text-[#7f9ebc] leading-relaxed">
                            Créez, suivez et classez vos interventions par priorité. Interface fluide pensée pour la réactivité.
                        </p>
                    </div>
                    <div class="cyber-card rounded-[20px] p-5">
                        <div class="flex items-center gap-2.5 mb-2">
                            <svg class="h-5 w-5 text-[#24e2b3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="font-bold text-[#dcefff] text-sm">Suivi opérationnel</span>
                        </div>
                        <p class="text-[13px] text-[#7f9ebc] leading-relaxed">
                            Rapports d'intervention, tableaux de bord et alertes en continu pour les techniciens.
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:hidden mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <ApplicationLogo class="h-10 w-10 text-[#43c9ff]" />
                    <div>
                        <p class="section-title text-lg text-[#dcefff]">ATECH Cybersécurité</p>
                        <p class="text-[10px] mono tracking-[0.14em] text-[#57c8ff] uppercase">SOC &bull; Intervention &bull; Supervision</p>
                    </div>
                </div>
                <h1 class="section-title text-[28px] leading-[1.15] text-[#dcefff] mb-3">
                    Pilotage des interventions techniques, en un seul espace.
                </h1>
                <p class="text-sm text-[#90aac8] leading-relaxed">
                    Centralisez vos tickets, suivez l'avancement et collaborez avec vos techniciens.
                </p>
            </div>

            <div>
                <div class="cyber-card rounded-[26px] p-7 sm:p-9">
                    <p class="text-[11px] mono tracking-[0.22em] text-[#57c8ff] uppercase mb-2">
                        ATECH &bull; PLATEFORME
                    </p>

                    <h2 class="section-title text-[22px] text-[#dcefff] leading-tight mb-3">
                        Accédez à votre espace de pilotage
                    </h2>

                    <p class="text-sm text-[#90aac8] leading-relaxed mb-6">
                        Plateforme de gestion des interventions de cybersécurité. Suivi en temps réel,
                        rapports détaillés et coordination entre clients et techniciens.
                    </p>

                    <div class="space-y-3 mb-6">
                        <div class="rounded-xl border border-[#1c3b5b] bg-[#0b1627]/60 px-4 py-3 flex items-start gap-3">
                            <span class="mt-0.5 flex-shrink-0 h-5 w-5 rounded-full bg-[#43c9ff]/15 flex items-center justify-center text-[11px] mono text-[#43c9ff] font-bold">1</span>
                            <span class="text-[13px] text-[#a7bed8]">Créez des tickets d'intervention avec niveau de priorité et pièces jointes.</span>
                        </div>
                        <div class="rounded-xl border border-[#1c3b5b] bg-[#0b1627]/60 px-4 py-3 flex items-start gap-3">
                            <span class="mt-0.5 flex-shrink-0 h-5 w-5 rounded-full bg-[#43c9ff]/15 flex items-center justify-center text-[11px] mono text-[#43c9ff] font-bold">2</span>
                            <span class="text-[13px] text-[#a7bed8]">Suivez l'avancement en direct : ouvert, en cours, rapport déposé, résolu.</span>
                        </div>
                        <div class="rounded-xl border border-[#1c3b5b] bg-[#0b1627]/60 px-4 py-3 flex items-start gap-3">
                            <span class="mt-0.5 flex-shrink-0 h-5 w-5 rounded-full bg-[#43c9ff]/15 flex items-center justify-center text-[11px] mono text-[#43c9ff] font-bold">3</span>
                            <span class="text-[13px] text-[#a7bed8]">Recevez les rapports d'intervention et validez la résolution des incidents.</span>
                        </div>
                    </div>

                    <p class="text-xs text-[#556e8c] mono text-center">
                        Authentification requise pour accéder à la plateforme.
                    </p>
                </div>

                <div class="flex gap-3 mt-5 lg:hidden">
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="btn-cyber flex-1 py-3 text-[14px] text-center"
                    >
                        Se connecter
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="flex-1 py-3 text-[14px] text-center rounded-xl border border-[#1c3b5b] bg-transparent text-[#a7bed8] font-semibold hover:border-[#22425e] transition-all duration-200"
                    >
                        S'inscrire
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

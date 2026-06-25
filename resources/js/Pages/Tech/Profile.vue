<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    evaluations: { type: Array, default: () => [] },
    moyenne: { type: Number, default: 0 },
    totalEvals: { type: Number, default: 0 },
})

const user = usePage().props.auth.user

const avatarInitial = (user?.name || '?').split(' ').map(s => s[0]).join('').slice(0, 2).toUpperCase()

function starDisplay(note) {
    return '★'.repeat(Math.round(note)) + '☆'.repeat(5 - Math.round(note))
}
</script>

<template>
    <Head title="Profil SOC" />
    <AppLayout>
        <div class="max-w-[720px] space-y-6">
            <div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-[#6bcbff] font-semibold mb-2 section-title">SOC PROFIL</div>
                <h2 class="section-title text-[28px] text-main">Profil technicien</h2>
            </div>

            <div class="cyber-card rounded-[20px] p-6">
                <div class="flex items-center gap-4">
                    <div class="w-[56px] h-[56px] rounded-2xl bg-[#16334a] border-2 border-[#2a607a] flex items-center justify-center text-[20px] font-bold text-[#8eddff] section-title shrink-0">
                        {{ avatarInitial }}
                    </div>
                    <div>
                        <p class="text-[18px] font-bold text-main">{{ user?.name }}</p>
                        <p class="text-[13px] text-sub">{{ user?.email }} &bull; {{ user?.specialite || 'SOC Analyst' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <div class="cyber-card rounded-[20px] p-5">
                    <p class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1">Note moyenne</p>
                    <p class="text-[24px] section-title font-bold text-[#ffcc3a]">{{ moyenne }} <span class="text-[14px] text-muted">/ 5</span></p>
                    <p class="text-[14px] text-[#ffcc3a] mt-1">{{ starDisplay(moyenne) }}</p>
                </div>
                <div class="cyber-card rounded-[20px] p-5">
                    <p class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1">Évaluations</p>
                    <p class="text-[24px] section-title font-bold text-main">{{ totalEvals }}</p>
                </div>
                <div class="cyber-card rounded-[20px] p-5">
                    <p class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1">Rôle</p>
                    <p class="text-[24px] section-title font-bold text-main">Technicien SOC</p>
                    <p class="text-[12px] text-muted">{{ user?.specialite || 'Général' }}</p>
                </div>
            </div>

            <div v-if="evaluations.length > 0" class="cyber-card rounded-[20px] p-6">
                <h4 class="section-title text-[16px] text-main mb-5">Évaluations reçues</h4>
                <div class="space-y-4">
                    <div v-for="ev in evaluations" :key="ev.id" class="border-b border-subtle pb-4 last:border-0 last:pb-0">
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] text-sub">{{ ev.client?.name || ev.client_name }}</span>
                            <span class="text-[#ffcc3a]">{{ '★'.repeat(ev.note) }}<span class="text-[#2a3a55]">{{ '★'.repeat(5 - ev.note) }}</span></span>
                        </div>
                        <p v-if="ev.commentaire" class="text-[13px] text-sub mt-1 whitespace-pre-line">{{ ev.commentaire }}</p>
                        <p class="text-[11px] text-muted mt-1">{{ ev.created_at }}</p>
                    </div>
                </div>
            </div>

            <div v-else class="cyber-card rounded-[20px] p-6">
                <p class="text-[14px] text-muted text-center py-4">Aucune évaluation reçue pour le moment.</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const user = computed(() => usePage().props.auth.user)
const showingNav = ref(false)
const showingUserMenu = ref(false)
const notifCount = ref(0)
const userMenuRef = ref(null)
const searchQuery = ref('')

const isDark = ref(false)

const themeBg = computed(() => isDark.value ? '#070c16' : '#f8fafc')
const themeSidebarBg = computed(() => isDark.value ? '#0F172A' : '#ffffff')
const themeHeaderBg = computed(() => isDark.value ? '#0F172A' : '#ffffff')
const themeBorder = computed(() => isDark.value ? '#1E3A52' : '#e2e8f0')
const themeText = computed(() => isDark.value ? '#dde7f6' : '#1e293b')
const themeTextMuted = computed(() => isDark.value ? '#515F7A' : '#64748b')
const themeTextSecondary = computed(() => isDark.value ? '#06B6D4' : '#FF9800')
const themeAccent = computed(() => '#FF9800')
const themeInputBg = computed(() => isDark.value ? '#1A2F47' : '#f1f5f9')
const themeHover = computed(() => isDark.value ? '#1A2F47' : '#f1f5f9')
const themeFooterText = computed(() => isDark.value ? '#3a4766' : '#94a3b8')

const navActiveBg = computed(() => isDark.value ? '#0b3b6c' : '#fef3c7')
const navActiveText = computed(() => isDark.value ? '#e4f8ff' : '#d97706')
const navActiveIcon = computed(() => isDark.value ? '#6ce9ff' : '#FF9800')
const navActiveBorder = computed(() => isDark.value ? '#2b8ed3' : '#fbbf24')
const navInactiveText = computed(() => isDark.value ? '#515F7A' : '#94a3b8')

function initTheme() {
  const saved = localStorage.getItem('atech_theme')
  if (saved === 'dark') {
    isDark.value = true
    document.documentElement.classList.add('dark')
  } else {
    isDark.value = false
    document.documentElement.classList.remove('dark')
  }
}

function toggleTheme() {
  isDark.value = !isDark.value
  localStorage.setItem('atech_theme', isDark.value ? 'dark' : 'light')
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

const iconDefs = {
  LayoutGrid: ['M2 2h5v5H2z','M10 2h5v5H10z','M2 10h5v5H2z','M10 10h5v5H10z'],
  PlusCircle: ['M8.5 2a6.5 6.5 0 100 13 6.5 6.5 0 000-13z','M8.5 5.5v6','M5.5 8.5h6'],
  Ticket: ['M2 4h9v9H2z','M6 2h9v9H6z'],
  Wrench: ['M4 13l4-4a2.5 2.5 0 014.5 0l3-3-2-2-5 5','M10 4.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'],
  User: ['M8.5 2a2.5 2.5 0 100 5 2.5 2.5 0 000-5z','M3 14.5v-1a5 5 0 0110 0v1'],
  Chart: ['M2 14h13','M4 11v3','M7 7v7','M10 9v5','M13 4v10'],
  Clipboard: ['M4 2h9a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V3a1 1 0 011-1z','M7 2v3','M6 8h5','M6 11h5','M6 14h3'],
  Users: ['M6 2a2 2 0 100 4 2 2 0 000-4z','M2 12.5v-1a3.5 3.5 0 017 0v1','M13.5 12.5v-1a3 3 0 00-1.5-2.5','M12 2a2 2 0 010 4'],
  Calendar: ['M3 3h11a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V5a2 2 0 012-2z','M1 8h15','M5 1v3','M12 1v3'],
  Star: ['M8.5 2l2.1 4.2 4.6.7-3.3 3.2.8 4.6-4.2-2.2-4.2 2.2.8-4.6-3.3-3.2 4.6-.7z'],
  Bell: ['M3 10a3.5 3.5 0 003.5 3.5h4a3.5 3.5 0 003.5-3.5 2 2 0 01-2-2V5.5A3.5 3.5 0 008.5 2 3.5 3.5 0 005 5.5v2.5a2 2 0 01-2 2z','M7 14a1.5 1.5 0 003 0'],
  Search: ['M10.5 7A4.5 4.5 0 116 2.5 4.5 4.5 0 0110.5 7z','M15 14.5l-3.5-3.5'],
  Sun: ['M8.5 1.5v2','M8.5 13.5v2','M3.5 3.5l1.4 1.4','M12.1 12.1l1.4 1.4','M1.5 8.5h2','M13.5 8.5h2','M3.5 13.5l1.4-1.4','M12.1 4.9l1.4-1.4','M8.5 4.5a4 4 0 100 8 4 4 0 000-8z'],
  Moon: ['M13.5 9.5a5 5 0 01-8 4.5 6 6 0 008-4.5z','M13.5 2.5v1','M13.5 12.5v1','M9.5 4l.7.7','M16.6 4l-.7.7','M9.5 12l.7-.7','M16.6 12l-.7-.7'],
}

const roleLabel = computed(() => {
  const map = { client:'Client', technicien:'Technicien', admin:'Administrateur' }
  return map[user.value?.role] || 'Membre'
})

const pageTitle = computed(() => {
  const c = usePage().component || ''
  const parts = c.split('/')
  const name = parts[parts.length - 1]
  const titles = { Dashboard:'Tableau de bord', Index:'Liste', Create:'Nouveau ticket', Show:'Détail', Edit:'Profil', Users:'Comptes & accès', Tickets:'Gestion tickets', Reviews:'Avis clients', ReportForm:'Rapport' }
  return titles[name] || name
})

const clientNavItems = [
  { label:'Tableau de bord', route:'dashboard', icon:'LayoutGrid' },
  { label:'Nouveau ticket', route:'tickets.create', icon:'PlusCircle' },
  { label:'Mes tickets', route:'tickets.index', icon:'Ticket' },
  { label:'Calendrier', route:null, icon:'Calendar' },
]
const techNavItems = [
  { label:'Mes interventions', route:'dashboard', icon:'Wrench' },
  { label:'Tickets assignés', route:'tickets.index', icon:'Ticket' },
  { label:'Profil SOC', route:'profile.edit', icon:'User' },
  { label:'Calendrier', route:null, icon:'Calendar' },
]
const adminNavItems = [
  { label:"Vue d'ensemble", route:'dashboard', icon:'Chart' },
  { label:'Gestion tickets', route:'admin.tickets', icon:'Clipboard' },
  { label:'Comptes & accès', route:'admin.users', icon:'Users' },
  { label:'Avis clients', route:'admin.reviews', icon:'Star' },
  { label:'Calendrier', route:null, icon:'Calendar' },
]

const navItems = computed(() => {
  if (user.value?.role === 'client') return clientNavItems
  if (user.value?.role === 'technicien') return techNavItems
  if (user.value?.role === 'admin') return adminNavItems
  return clientNavItems
})

function isActive(r) { return r ? route().current(r + '*') : false }

async function fetchNotifCount() {
  try { const r = await fetch('/notifications/count'); const d = await r.json(); notifCount.value = typeof d === 'number' ? d : (d.count||0) } catch {}
}

function handleClickOutside(e) { if (userMenuRef.value && !userMenuRef.value.contains(e.target)) showingUserMenu.value = false }
function handleEscape(e) { if (e.key === 'Escape') { showingUserMenu.value = false; showingNav.value = false } }

onMounted(() => {
  initTheme()
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleEscape)
  fetchNotifCount()
})
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleEscape)
})
</script>

<template>
  <div class="flex min-h-screen g-grid font-['Inter'] antialiased" :style="{ background: themeBg, color: themeText }">
    <div v-if="showingNav" @click="showingNav = false" class="fixed inset-0 z-40 bg-black/55 backdrop-blur-sm lg:hidden" />

    <aside
      :class="['fixed inset-y-0 left-0 z-50 w-[280px] flex flex-col transition-transform duration-300 overflow-hidden', showingNav ? 'translate-x-0' : '-translate-x-full', 'lg:translate-x-0']"
      :style="{ background: themeSidebarBg, borderRight: `1px solid ${themeBorder}` }"
    >
      <div class="shrink-0 px-5 pt-7 pb-5 flex items-center gap-3">
        <div class="w-12 h-12 rounded-lg overflow-hidden flex items-center justify-center shrink-0" :style="{ background: themeInputBg }">
          <img src="https://atech-cybersecurite.com/assets/img/logo-mark.png" alt="ATECH logo" class="w-12 h-12 object-contain" @error="$event.target.style.display='none';$event.target.nextElementSibling.style.display='flex'" />
          <span class="hidden font-bold text-lg" :style="{ color: themeAccent, fontFamily: '\'Space Grotesk\',sans-serif' }">A</span>
        </div>
        <div class="min-w-0">
          <div class="font-bold text-lg tracking-[0.18em] uppercase leading-tight truncate" :style="{ color: themeAccent, fontFamily: '\'Space Grotesk\',sans-serif' }">ATECH</div>
          <div class="text-[11px] font-medium leading-tight" :style="{ color: themeTextSecondary }">Cybersécurité</div>
        </div>
        <button @click="showingNav = false" class="lg:hidden ml-auto p-2 rounded-lg transition-colors" :style="{ color: themeTextMuted }" @mouseenter="$event.target.style.background=themeHover;$event.target.style.color=themeText" @mouseleave="$event.target.style.background='transparent';$event.target.style.color=themeTextMuted">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <nav class="flex-1 px-4 py-2 space-y-1 overflow-y-auto">
        <template v-for="item in navItems" :key="item.label">
          <Link v-if="item.route" :href="route(item.route)"
            class="flex items-center gap-3 px-3 py-[13px] rounded-[16px] text-[14px] font-medium transition-all duration-200"
            :style="isActive(item.route)
              ? { background: navActiveBg, color: navActiveText, border: `1px solid ${navActiveBorder}`, boxShadow: '0 8px 20px rgba(18,117,211,0.18)' }
              : { background: 'transparent', color: navInactiveText }">
            <svg class="w-[17px] h-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 17 17" :style="{ color: isActive(item.route) ? navActiveIcon : undefined }">
              <path v-for="(d,i) in iconDefs[item.icon]" :key="i" stroke-linecap="round" stroke-linejoin="round" :d="d"/>
            </svg>
            <span class="truncate">{{ item.label }}</span>
          </Link>
          <button v-else class="flex items-center gap-3 px-3 py-[13px] rounded-[16px] text-[14px] font-medium transition-all duration-200 w-full text-left bg-transparent" :style="{ color: navInactiveText }">
            <svg class="w-[17px] h-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 17 17">
              <path v-for="(d,i) in iconDefs[item.icon]" :key="i" stroke-linecap="round" stroke-linejoin="round" :d="d"/>
            </svg>
            <span class="truncate">{{ item.label }}</span>
          </button>
        </template>
      </nav>

      <div class="shrink-0 px-5 py-4" :style="{ borderTop: `1px solid ${themeBorder}` }">
        <div class="text-[11px] font-semibold tracking-wide" :style="{ color: themeTextMuted, fontFamily: '\'Space Grotesk\',sans-serif' }">ATECH v4.2.1</div>
        <div class="text-[10px] mt-0.5" :style="{ color: themeFooterText }">© 2026 ATECH Cybersécurité</div>
      </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 lg:ml-[280px]">
      <header class="sticky top-0 z-30 h-20 frost flex items-center px-4 sm:px-6 gap-3 shrink-0" :style="{ background: `${themeHeaderBg}${isDark ? 'f2' : 'f2'}`, borderBottom: `1px solid ${themeBorder}` }">
        <button @click="showingNav = true" class="lg:hidden p-2 -ml-1 rounded-lg transition-colors" :style="{ color: themeTextMuted }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <div class="hidden sm:block min-w-0">
          <div class="text-[10px] uppercase tracking-[0.15em] font-semibold leading-none mb-1" :style="{ color: themeTextMuted }">Espace {{ roleLabel }}</div>
          <h1 class="text-[17px] font-bold leading-tight truncate section-title" :style="{ color: themeText }">{{ pageTitle }}</h1>
        </div>

        <div class="flex-1" />

        <div class="hidden sm:flex items-center gap-2 rounded-xl border px-3 py-2 w-60 focus-within:border-[#2b8ed3] transition-colors" :style="{ background: themeInputBg, borderColor: themeBorder }">
          <svg class="w-[17px] h-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 17 17" :style="{ color: themeTextMuted }">
            <path v-for="(d,i) in iconDefs.Search" :key="i" stroke-linecap="round" stroke-linejoin="round" :d="d"/>
          </svg>
          <input v-model="searchQuery" type="text" placeholder="Rechercher ticket, ref…" class="bg-transparent text-sm outline-none w-full leading-normal" :style="{ color: themeText }" />
        </div>

        <!-- Theme toggle -->
        <button @click="toggleTheme" class="p-2 rounded-lg transition-colors" :style="{ color: themeTextMuted }" :title="isDark ? 'Mode clair' : 'Mode sombre'">
          <svg v-if="isDark" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 17 17">
            <path v-for="(d,i) in iconDefs.Sun" :key="i" stroke-linecap="round" stroke-linejoin="round" :d="d"/>
          </svg>
          <svg v-else class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 17 17">
            <path v-for="(d,i) in iconDefs.Moon" :key="i" stroke-linecap="round" stroke-linejoin="round" :d="d"/>
          </svg>
        </button>

        <!-- Notifications -->
        <button @click="router.get(route('notifications.index'))" class="relative p-2 rounded-lg transition-colors" :style="{ color: themeTextMuted }">
          <svg class="w-[20px] h-[20px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 17 17">
            <path v-for="(d,i) in iconDefs.Bell" :key="i" stroke-linecap="round" stroke-linejoin="round" :d="d"/>
          </svg>
          <span v-if="notifCount>0" class="absolute top-1 right-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[#FF9800] text-[10px] font-bold text-white px-1 leading-none">{{ notifCount>99?'99+':notifCount }}</span>
        </button>

        <!-- User dropdown -->
        <div ref="userMenuRef" class="relative">
          <button @click="showingUserMenu = !showingUserMenu" class="flex items-center gap-2 p-1.5 rounded-lg transition-colors" :style="{ '--hover-bg': themeHover }">
            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-bold shrink-0" :style="{ background: themeInputBg, borderColor: themeAccent, color: themeAccent, fontFamily: '\'Space Grotesk\',sans-serif' }">{{ user?.name?.charAt(0)?.toUpperCase() || '?' }}</div>
            <div class="hidden sm:block text-left leading-tight min-w-0 max-w-[120px]">
              <div class="text-sm truncate font-medium" :style="{ color: themeText }">{{ user?.name }}</div>
              <div class="text-[10px] capitalize" :style="{ color: themeTextSecondary }">{{ user?.role }}</div>
            </div>
            <svg class="w-4 h-4 hidden sm:block" :style="{ color: themeTextMuted }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 scale-95 -translate-y-1" enter-to-class="opacity-100 scale-100 translate-y-0" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="showingUserMenu" class="absolute right-0 mt-2 w-48 rounded-xl shadow-2xl py-1 origin-top-right z-50" :style="{ background: themeSidebarBg, border: `1px solid ${themeBorder}`, boxShadow: isDark ? '0 25px 50px rgba(0,0,0,.5)' : '0 10px 40px rgba(0,0,0,.15)' }">
              <div class="px-4 py-3 sm:hidden" :style="{ borderBottom: `1px solid ${themeBorder}` }">
                <div class="text-sm font-medium truncate" :style="{ color: themeText }">{{ user?.name }}</div>
                <div class="text-[11px] capitalize" :style="{ color: themeTextSecondary }">{{ user?.role }}</div>
              </div>
              <Link :href="route('profile.edit')" class="flex items-center gap-2 px-4 py-2.5 text-sm transition-colors" :style="{ color: themeText }" @mouseenter="$event.target.style.background=themeHover" @mouseleave="$event.target.style.background='transparent'" @click="showingUserMenu=false">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 17 17"><path v-for="(d,i) in iconDefs.User" :key="i" stroke-linecap="round" stroke-linejoin="round" :d="d"/></svg> Profil
              </Link>
              <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-2 w-full text-left px-4 py-2.5 text-sm transition-colors" :style="{ color: isDark ? '#f87171' : '#dc2626' }" @mouseenter="$event.target.style.background=themeHover" @mouseleave="$event.target.style.background='transparent'" @click="showingUserMenu=false">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg> Déconnexion
              </Link>
            </div>
          </transition>
        </div>
      </header>

      <main class="flex-1">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-7 py-7">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

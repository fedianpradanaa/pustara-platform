<template>
    <header class="relative z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6 lg:ml-72 lg:justify-end">
        <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden"
            aria-label="Buka menu navigasi"
            aria-controls="dashboard-sidebar"
            :aria-expanded="sidebarOpen"
            @click="sidebarOpen = true"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
            </div>
            <div class="relative">
                <button
                    type="button"
                    class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-label="Menu akun"
                    :aria-expanded="profileMenuOpen"
                    @click="profileMenuOpen = !profileMenuOpen"
                >
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span>
                    <span v-if="user.name" class="hidden max-w-36 truncate text-sm font-semibold sm:block">{{ user.name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div v-if="profileMenuOpen" class="absolute right-0 top-full z-30 mt-2 w-40 rounded-xl border border-slate-100 bg-white p-1.5 shadow-lg">
                    <button type="button" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="logout">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-.943a.75.75 0 1 0-1.004-1.114l-2.5 2.25a.75.75 0 0 0 0 1.114l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-.943h9.546A.75.75 0 0 0 19 10Z" clip-rule="evenodd" />
                        </svg>
                        Sign out
                    </button>
                </div>
            </div>
        </div>
    </header>

    <button v-if="sidebarOpen" type="button" class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden" aria-label="Tutup menu navigasi" @click="sidebarOpen = false"></button>

    <aside
        id="dashboard-sidebar"
        class="dashboard-sidebar fixed inset-y-0 left-0 z-50 flex w-[min(85vw,18rem)] flex-col border-r border-slate-200 bg-white shadow-xl transition duration-300 ease-out motion-reduce:transition-none lg:w-72 lg:shadow-none"
        :class="{ 'sidebar-closed': !sidebarOpen }"
    >
        <div class="flex min-h-24 items-center justify-between gap-4 border-b border-slate-100 px-5 py-5">
            <a href="/home" class="flex min-w-0 items-center" :aria-label="`${$brand.name} dashboard`">
                <img v-if="$brand.logo_path" :src="'/' + $brand.logo_path" :alt="$brand.name" class="max-h-12 w-auto max-w-44 object-contain object-left" />
                <span v-else class="text-xl font-bold text-slate-900">{{ $brand.name }}</span>
            </a>
            <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden" aria-label="Tutup menu navigasi" @click="sidebarOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="sidebar-nav min-h-0 flex-1 overflow-y-auto px-3 py-5" aria-label="Navigasi dashboard">
            <p class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">Menu utama</p>
            <component :is="loading ? 'EmptyMenu' : currentComponent" />
        </nav>

        <div class="border-t border-slate-100 px-4 py-4">
            <div class="rounded-xl bg-blue-50 px-4 py-3">
                <p class="truncate text-sm font-semibold text-blue-800">{{ $brand.name }}</p>
                <p class="mt-1 text-xs text-slate-500">Dashboard pengelolaan</p>
            </div>
        </div>
    </aside>

    <main class="box-border h-[calc(100vh-4rem)] overflow-auto bg-slate-100 p-4 lg:ml-72"><router-view></router-view></main>
</template>

<script>
import MenuAdmin from './components/MenuAdmin.vue'
import MenuSupplier from './components/MenuSupplier.vue'
import MenuDistributor from './components/MenuDistributor.vue'
import EmptyMenu from './components/EmptyMenu.vue'

export default {
    components: {
        MenuAdmin,
        MenuSupplier,
        MenuDistributor,
        EmptyMenu,
    },

    data() {
        return {
            loading: true,
            user: {},
            sidebarOpen: false,
            profileMenuOpen: false,
        }
    },

    computed: {
        currentComponent() {
            return this.user.role === 0 ? 'MenuAdmin' : this.user.role === 1 ? 'MenuSupplier' : 'MenuDistributor'
        },
    },

    watch: {
        $route() {
            this.sidebarOpen = false
            this.profileMenuOpen = false
        },
    },

    created() {
        this.userinfo()
    },

    mounted() {
        window.addEventListener('resize', this.handleResize)
        document.addEventListener('keydown', this.handleKeydown)
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.handleResize)
        document.removeEventListener('keydown', this.handleKeydown)
    },

    methods: {
        userinfo() {
            this.user = {}

            window.axios
                .get('/userinfo')
                .then((response) => {
                    this.user = response.data
                    this.loading = false
                })
                .catch((e) => {
                    console.error(e)
                })
        },

        async logout() {
            await window.axios.post('/logout').then((e) => {
                this.$notyf.success('Logout successful!')
                window.location = '/'
            })
        },

        handleResize() {
            if (window.innerWidth >= 1024) {
                this.sidebarOpen = false
            }
        },

        handleKeydown(event) {
            if (event.key === 'Escape') {
                this.sidebarOpen = false
                this.profileMenuOpen = false
            }
        },
    },
}
</script>

<style scoped>
@media (max-width: 1023px) {
    .dashboard-sidebar.sidebar-closed {
        visibility: hidden;
        transform: translateX(-100%);
    }
}

.sidebar-nav :deep(> ul) {
    max-height: none;
    overflow: visible;
}

.sidebar-nav :deep(a),
.sidebar-nav :deep(summary) {
    border-radius: 0.75rem;
    color: #475569;
    padding: 0.75rem;
    transition: background-color 150ms ease, color 150ms ease;
}

.sidebar-nav :deep(a:hover),
.sidebar-nav :deep(summary:hover),
.sidebar-nav :deep(details[open] > summary) {
    background-color: rgb(var(--brand-blue-50));
    color: rgb(var(--brand-blue-700));
}

.sidebar-nav :deep(a.router-link-exact-active) {
    background-color: rgb(var(--brand-blue-50));
    color: rgb(var(--brand-blue-700));
    box-shadow: inset 3px 0 0 rgb(var(--brand-blue-600));
}

.sidebar-nav :deep(a:focus-visible),
.sidebar-nav :deep(summary:focus-visible) {
    outline: 2px solid rgb(var(--brand-blue-500));
    outline-offset: 2px;
}

.sidebar-nav :deep(a > svg),
.sidebar-nav :deep(summary span > svg[stroke]) {
    flex-shrink: 0;
    stroke: currentColor;
}

.sidebar-nav :deep(details ul) {
    margin: 0.25rem 0 0.5rem 1.35rem;
    padding: 0 0 0 0.5rem;
    border-left: 1px solid #e2e8f0;
}

.sidebar-nav :deep(details ul a) {
    font-weight: 500;
    padding: 0.625rem 0.75rem;
}
</style>

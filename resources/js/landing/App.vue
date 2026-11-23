<template>
    <div class="min-h-screen bg-white text-slate-800">
        <header class="border-b border-slate-100 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-5 lg:px-8" aria-label="Navigasi utama">
                <a href="/" class="flex shrink-0 items-center" :aria-label="`${$brand.name} beranda`">
                    <img v-if="$brand.logo_path" :src="'/' + $brand.logo_path" :alt="$brand.name" class="h-10 max-w-40 object-contain object-left sm:max-w-52" />
                    <span v-else class="text-xl font-bold text-slate-900">{{ $brand.name }}</span>
                </a>
                <div class="flex items-center gap-4 sm:gap-8">
                    <a href="#tentang" class="hidden text-sm font-medium text-slate-600 transition hover:text-blue-700 md:inline">Tentang</a>
                    <a href="#layanan" class="hidden text-sm font-medium text-slate-600 transition hover:text-blue-700 md:inline">Keunggulan</a>
                    <a href="#kontak" class="hidden text-sm font-medium text-slate-600 transition hover:text-blue-700 md:inline">Kontak</a>
                    <a href="/login" class="rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Masuk</a>
                </div>
            </nav>
        </header>

        <main>
            <section class="relative isolate overflow-hidden bg-gradient-to-br from-blue-50 via-white to-white">
                <div class="mx-auto grid max-w-7xl items-center gap-14 px-6 py-20 sm:py-24 lg:grid-cols-2 lg:gap-20 lg:px-8 lg:py-28">
                    <div class="max-w-2xl">
                        <p class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-blue-700 shadow-sm ring-1 ring-blue-100">
                            <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                            {{ $brand.landing.eyebrow }}
                        </p>
                        <h1 class="mt-7 text-4xl font-bold leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            {{ $brand.landing.hero_title }}
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">{{ $brand.landing.hero_description }}</p>
                        <div class="mt-9 flex flex-wrap gap-3">
                            <a href="/pendaftaran-client" class="inline-flex items-center justify-center rounded-full bg-blue-700 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Daftarkan perpustakaan <span aria-hidden="true" class="ml-2">&rarr;</span>
                            </a>
                            <a href="#layanan" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-6 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-700">
                                Jelajahi keunggulan
                            </a>
                        </div>
                        <p class="mt-7 text-sm leading-6 text-slate-500">{{ $brand.landing.hero_note }}</p>
                    </div>

                    <div class="relative mx-auto w-full max-w-lg">
                        <div aria-hidden="true" class="absolute -left-5 -top-5 h-32 w-32 rounded-full bg-blue-100 blur-2xl"></div>
                        <div aria-hidden="true" class="absolute -bottom-6 -right-4 h-40 w-40 rounded-full bg-blue-200 blur-3xl"></div>
                        <div class="relative overflow-hidden rounded-3xl bg-blue-900 p-7 text-white shadow-2xl shadow-blue-900/20 sm:p-9">
                            <div class="flex items-center justify-between gap-4 border-b border-white/20 pb-6">
                                <span class="text-sm font-bold uppercase tracking-[0.18em] text-blue-100">{{ $brand.name }}</span>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-white">Ruang baca digital</span>
                            </div>
                            <p class="mt-8 text-sm font-medium text-blue-100">Untuk lembaga dan komunitas</p>
                            <p class="mt-3 max-w-sm text-2xl font-semibold leading-snug sm:text-3xl">{{ $brand.landing.audience_title }}</p>
                            <p class="mt-4 text-sm leading-7 text-blue-100">{{ $brand.landing.audience_description }}</p>
                            <div class="mt-8 flex flex-wrap gap-2" aria-label="Jenjang pembaca">
                                <span v-for="audience in ['SD', 'SMP', 'SMA', 'Perguruan tinggi', 'Umum']" :key="audience" class="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold">{{ audience }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="tentang" class="mx-auto grid max-w-7xl scroll-mt-8 gap-7 px-6 py-20 sm:py-24 lg:grid-cols-2 lg:gap-20 lg:px-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">{{ $brand.landing.about_label }}</p>
                    <h2 class="mt-4 max-w-xl text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ $brand.landing.about_title }}</h2>
                </div>
                <p class="max-w-xl text-base leading-8 text-slate-600 lg:pt-8">{{ $brand.landing.about_description }}</p>
            </section>

            <section id="layanan" class="scroll-mt-8 bg-slate-50">
                <div class="mx-auto max-w-7xl px-6 py-20 sm:py-24 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">{{ $brand.landing.services_label }}</p>
                        <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ $brand.landing.services_title }}</h2>
                        <p class="mt-5 text-base leading-7 text-slate-600">{{ $brand.landing.services_description }}</p>
                    </div>
                    <div class="mt-10 grid gap-5 md:grid-cols-3">
                        <article v-for="(service, index) in $brand.landing.services" :key="service.title" class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-sm font-bold text-blue-700">{{ String(index + 1).padStart(2, '0') }}</span>
                            <h3 class="mt-6 text-lg font-bold text-slate-900">{{ service.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ service.description }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="kontak" class="mx-auto max-w-7xl scroll-mt-8 px-6 py-20 sm:py-24 lg:px-8">
                <div class="rounded-3xl bg-blue-50 px-7 py-12 sm:px-12 sm:py-16">
                    <div class="max-w-3xl">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">Langkah berikutnya</p>
                        <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">{{ $brand.landing.call_to_action }}</h2>
                        <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600">{{ $brand.landing.call_to_action_description }}</p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="/pendaftaran-client" class="inline-flex items-center justify-center rounded-full bg-blue-700 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Daftarkan perpustakaan</a>
                            <a :href="`mailto:${$brand.contact_email}`" class="inline-flex items-center justify-center rounded-full border border-blue-200 bg-white px-6 py-3.5 text-sm font-semibold text-blue-700 transition hover:border-blue-400">{{ $brand.landing.contact_label }}</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-8 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <p class="font-semibold text-slate-900">{{ $brand.name }}</p>
                <a :href="`mailto:${$brand.contact_email}`" class="transition hover:text-blue-700">{{ $brand.contact_email }}</a>
                <p>&copy; {{ new Date().getFullYear() }} {{ $brand.name }}</p>
            </div>
        </footer>
    </div>
</template>

<template>
    <div class="min-h-screen bg-white text-slate-800">
        <header class="border-b border-slate-200">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8" aria-label="Navigasi utama">
                <a href="/" class="flex items-center" aria-label="Pustaka Nusantara beranda">
                    <img src="/images/logo/logo.png" alt="Pustaka Nusantara" class="h-10 w-auto" />
                </a>

                <div class="flex items-center gap-3 sm:gap-8">
                    <a href="#tentang" class="hidden text-sm font-medium text-slate-600 transition hover:text-blue-700 sm:inline">Tentang</a>
                    <a href="#layanan" class="hidden text-sm font-medium text-slate-600 transition hover:text-blue-700 sm:inline">Layanan</a>
                    <a href="#kontak" class="hidden text-sm font-medium text-slate-600 transition hover:text-blue-700 sm:inline">Kontak</a>
                    <a
                        href="/login"
                        class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Masuk
                    </a>
                </div>
            </nav>
        </header>

        <main>
            <section class="relative isolate overflow-hidden border-b border-slate-100">
                <div class="absolute inset-0 -z-10 bg-gradient-to-br from-blue-50 via-white to-white"></div>
                <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8 lg:py-40">
                    <div class="max-w-3xl">
                        <p class="mb-6 text-sm font-semibold uppercase tracking-[0.2em] text-blue-700">Selamat datang di Pustaka Nusantara</p>
                        <h1 class="text-4xl font-bold leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            Perpustakaan digital, lebih dekat dan mudah diakses.
                        </h1>
                        <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-600">
                            Pustaka Nusantara adalah ruang digital untuk menemukan dan menikmati koleksi bacaan. Akses layanan perpustakaan dengan praktis melalui satu platform.
                        </p>
                        <div class="mt-10 flex flex-wrap items-center gap-4">
                            <a
                                href="/login"
                                class="rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                Masuk ke akun
                            </a>
                            <a href="#tentang" class="rounded-lg border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-white">
                                Kenali Pustaka Nusantara
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="tentang" class="mx-auto max-w-7xl scroll-mt-8 px-6 py-20 lg:px-8 lg:py-24">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Tentang platform</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Membuka akses ke pengalaman membaca digital.</h2>
                    <p class="mt-6 text-base leading-7 text-slate-600">
                        Kami percaya akses terhadap bacaan dan pengetahuan sebaiknya terasa sederhana. Pustaka Nusantara menghadirkan pengalaman perpustakaan digital yang bisa dijangkau dari perangkat Anda.
                    </p>
                </div>
            </section>

            <section id="layanan" class="scroll-mt-8 bg-slate-50">
                <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Mengapa Pustaka Nusantara</p>
                        <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Fokus pada hal yang penting: membaca.</h2>
                    </div>
                    <div class="mt-12 grid gap-8 sm:grid-cols-3">
                        <article class="border-t-2 border-blue-700 pt-5">
                            <h3 class="text-lg font-semibold text-slate-900">Akses praktis</h3>
                            <p class="mt-3 leading-7 text-slate-600">Buka layanan perpustakaan digital melalui browser, tanpa perlu memasang aplikasi tambahan.</p>
                        </article>
                        <article class="border-t-2 border-blue-700 pt-5">
                            <h3 class="text-lg font-semibold text-slate-900">Koleksi dalam satu tempat</h3>
                            <p class="mt-3 leading-7 text-slate-600">Jelajahi koleksi bacaan yang tersedia melalui satu layanan yang mudah digunakan.</p>
                        </article>
                        <article class="border-t-2 border-blue-700 pt-5">
                            <h3 class="text-lg font-semibold text-slate-900">Untuk komunitas pembaca</h3>
                            <p class="mt-3 leading-7 text-slate-600">Mendukung pengalaman perpustakaan digital bagi pembaca dan pengelola koleksi.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-6 py-20 text-center lg:px-8 lg:py-24">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Mulai sekarang</p>
                <h2 class="mx-auto mt-4 max-w-2xl text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Siap menjelajahi Pustaka Nusantara?</h2>
                <p class="mx-auto mt-5 max-w-xl leading-7 text-slate-600">Masuk ke akun Anda untuk melanjutkan ke layanan perpustakaan.</p>
                <a
                    href="/login"
                    class="mt-8 inline-flex rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Masuk
                </a>
            </section>
        </main>

        <footer id="kontak" class="border-t border-slate-200 bg-slate-50">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-8 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <p class="font-semibold text-slate-800">Pustaka Nusantara</p>
                <a href="mailto:info@pustakanusantara.id" class="transition hover:text-blue-700">info@pustakanusantara.id</a>
                <p>&copy; {{ new Date().getFullYear() }} Pustaka Nusantara</p>
            </div>
        </footer>
    </div>
</template>

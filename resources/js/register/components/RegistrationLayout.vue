<template>
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-4xl items-center justify-between gap-4 px-4 py-4 sm:px-8">
                <a href="/" :aria-label="`Kembali ke ${$brand.name}`">
                    <img v-if="$brand.logo_path" :src="'/' + $brand.logo_path" :alt="$brand.name" class="max-h-12 w-auto max-w-40 object-contain" />
                    <span v-else class="text-lg font-bold">{{ $brand.name }}</span>
                </a>
                <a href="/" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Kembali ke beranda</a>
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-8 sm:py-12">
            <div class="mb-6">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-700">{{ $brand.name }}</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Pendaftaran {{ title }}</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Lengkapi formulir berikut untuk memulai pendaftaran bersama {{ $brand.name }}.</p>
            </div>
            <div class="registration-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                <slot />
            </div>
        </main>
    </div>
</template>

<script>
export default {
    props: {
        title: { type: String, required: true },
    },
}
</script>

<style scoped>
.registration-card :deep(form) {
    row-gap: 1.5rem;
}

.registration-card :deep(label.relative) {
    border-radius: 0.75rem;
}

.registration-card :deep(label.relative > input) {
    min-height: 3rem;
    padding-left: 0.875rem;
    padding-right: 0.875rem;
}

.registration-card :deep(label.relative > span) {
    top: 0;
    font-size: 0.75rem;
}

.registration-card :deep(select),
.registration-card :deep(textarea) {
    min-height: 3rem;
    border-radius: 0.75rem;
}

.registration-card :deep(button[type='submit']),
.registration-card :deep(form button:not([type])) {
    border-radius: 0.75rem;
}
</style>

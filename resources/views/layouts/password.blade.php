<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title') | {{ config('brand.current.name') }}</title>
    @include('layouts.favicon')
    @include('layouts.brand-theme')
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-50 px-4 py-10 text-slate-800">
    <main class="mx-auto max-w-md">
        <a href="/login" class="mb-8 flex justify-center">
            <img src="{{ asset(config('brand.current.logo_path')) }}" alt="{{ config('brand.current.name') }}" class="max-h-20 max-w-48 object-contain" />
        </a>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg sm:p-8">
            @yield('content')
        </div>
    </main>
</body>
</html>

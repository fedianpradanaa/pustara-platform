<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>Pustaka Nusantara</title>
        <meta name="description" content="Pustaka Nusantara adalah perpustakaan digital untuk menemukan dan menikmati koleksi bacaan dengan mudah." />
        <link rel="icon" href="{{ asset('images/logo/favicon.svg') }}" type="image/svg+xml" />

        <meta property="og:type" content="website" />
        <meta property="og:title" content="Pustaka Nusantara" />
        <meta property="og:description" content="Perpustakaan digital, lebih dekat dan mudah diakses. Temukan dan nikmati koleksi bacaan melalui Pustaka Nusantara." />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:image" content="{{ asset('images/logo/favicon.svg') }}" />
        <meta property="og:image:type" content="image/svg+xml" />
        <meta property="og:image:width" content="3000" />
        <meta property="og:image:height" content="3000" />
        <meta property="og:image:alt" content="Logo Pustaka Nusantara" />

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="Pustaka Nusantara" />
        <meta name="twitter:description" content="Perpustakaan digital, lebih dekat dan mudah diakses." />
        <meta name="twitter:image" content="{{ asset('images/logo/favicon.svg') }}" />

        <style>
            html {
                scroll-behavior: smooth;
            }
        </style>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/landing/app.js'])
    </head>
    <body>
        <div id="app">
            <main class="py-4">
                <div id="landing-container"></div>
            </main>
        </div>
    </body>
</html>

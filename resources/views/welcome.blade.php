<!DOCTYPE html>
@php($brand = config('brand.current'))
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ $brand['page_title'] }}</title>
        <meta name="description" content="{{ $brand['description'] }}" />
        @include('layouts.favicon')
        @include('layouts.brand-theme')

        <meta property="og:type" content="website" />
        <meta property="og:title" content="{{ $brand['social_title'] }}" />
        <meta property="og:description" content="{{ $brand['social_description'] }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        @if ($brand['social_image_path'])
            <meta property="og:image" content="{{ asset($brand['social_image_path']) }}" />
            <meta property="og:image:type" content="image/png" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
            <meta property="og:image:alt" content="{{ $brand['name'] }}, perpustakaan digital" />
        @endif

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $brand['social_title'] }}" />
        <meta name="twitter:description" content="{{ $brand['social_description'] }}" />
        @if ($brand['social_image_path'])
            <meta name="twitter:image" content="{{ asset($brand['social_image_path']) }}" />
        @endif

        <style>
            html {
                scroll-behavior: smooth;
            }
        </style>
        
        <!-- Scripts -->
        <script>
            window.brandConfig = @json($brand);
        </script>
        @vite(['resources/css/app.css', 'resources/js/landing/app.js'])
    </head>
    <body>
        <div id="app">
            <div id="landing-container"></div>
        </div>
    </body>
</html>

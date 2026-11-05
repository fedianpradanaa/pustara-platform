<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>GIA e-Library</title>

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

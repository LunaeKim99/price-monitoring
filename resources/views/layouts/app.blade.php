<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" :class="{ 'dark': isDark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Price Monitoring') - {{ config('app.name', 'Price Monitoring') }}</title>
    <script>
        (function() {
            var t = localStorage.getItem('pm-theme') || 'system';
            var d = t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (d) document.documentElement.classList.add('dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-secondary text-text-primary antialiased">
    <div class="min-h-screen flex">
        @include('components.sidebar')

        <div class="flex-1 flex flex-col">
            @include('components.navbar')

            <main class="flex-1 p-6">
                @if (session('success'))
                    @include('components.alert', ['type' => 'success', 'message' => session('success')])
                @endif

                @if (session('error'))
                    @include('components.alert', ['type' => 'error', 'message' => session('error')])
                @endif

                @yield('content')
            </main>

            <footer class="border-t border-border p-4 text-center text-sm text-text-muted dark:border-gray-700 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

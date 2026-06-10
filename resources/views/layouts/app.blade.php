<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-init="$store.theme.init()" :class="{ 'dark': $store.theme.isDark }">
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
<body class="bg-surface-secondary text-text-primary antialiased min-h-screen flex flex-col">
    <x-navbar />

    @if (session('success'))
        @include('components.alert', ['type' => 'success', 'message' => session('success')])
    @endif

    @if (session('error'))
        @include('components.alert', ['type' => 'error', 'message' => session('error')])
    @endif

    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

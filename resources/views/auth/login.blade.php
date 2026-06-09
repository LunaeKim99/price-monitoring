<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeManager()" :class="{ 'dark': isDark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>
    <script>
        (function() {
            var t = localStorage.getItem('pm-theme') || 'system';
            var d = t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (d) document.documentElement.classList.add('dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-secondary text-text-primary font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-surface rounded-xl shadow-sm border border-border">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center gap-2 mb-3">
                    <svg class="w-10 h-10 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-text-primary">{{ config('app.name') }}</h1>
                <p class="text-sm text-text-muted mt-1">Commodity Price Monitoring</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/30 dark:border-red-700">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                           required autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" id="password"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 border px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-text-secondary">Remember me</label>
                </div>

                <button type="submit" class="btn-primary w-full">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>

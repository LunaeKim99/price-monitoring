<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Price Monitoring') - {{ config('app.name', 'Price Monitoring') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">
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

            <footer class="border-t border-gray-200 p-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

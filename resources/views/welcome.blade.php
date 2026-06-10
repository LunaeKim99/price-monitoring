<!DOCTYPE html>
<html lang="en" x-data="themeManager()" :class="{ 'dark': isDark }" style="scroll-behavior: smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PriceWatch — Commodity Price Monitoring Dashboard. Pantau harga komoditas, prediksi tren pasar dengan AI.">
    <title>PriceWatch — Commodity Price Monitoring Dashboard</title>

    {{-- Anti-flash dark mode script --}}
    <script>
        (function(){var t=localStorage.getItem('pm-theme');if(t==='dark'||(t!=='light'&&window.matchMedia('(prefers-color-scheme:dark)').matches)){document.documentElement.classList.add('dark')}})();
    </script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float1 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        @keyframes float2 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        @keyframes float3 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .float-1 { animation: float1 4s ease-in-out infinite; }
        .float-2 { animation: float2 5s ease-in-out infinite; }
        .float-3 { animation: float3 3.5s ease-in-out infinite; }
        html { scroll-padding-top: 80px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-surface-secondary text-text-primary">

{{-- ===== SECTION 1: LANDING NAVBAR ===== --}}
<nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-surface/80 dark:bg-surface/80 backdrop-blur-lg border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- 1. LEFT: Logo --}}
            <div class="flex items-center gap-2.5 shrink-0">
                <svg class="w-8 h-8 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                    <polyline points="17 6 23 6 23 12"/>
                </svg>
                <span class="text-xl font-heading font-bold text-brand-600">PriceWatch</span>
            </div>

            {{-- 2. CENTER: Nav links --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="#fitur" class="text-sm font-medium text-text-secondary hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Fitur</a>
                <a href="#cara-kerja" class="text-sm font-medium text-text-secondary hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Cara Kerja</a>
                <a href="#tentang" class="text-sm font-medium text-text-secondary hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Tentang</a>
            </div>

            {{-- 3. RIGHT: Actions --}}
            <div class="flex items-center gap-3">
                {{-- Dark mode toggle --}}
                <button @click="setTheme(isDark ? 'light' : 'dark')" class="p-2 rounded-lg text-text-secondary hover:bg-surface-hover transition-colors" aria-label="Toggle theme">
                    <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                {{-- Desktop buttons --}}
                <a href="{{ route('login') }}" class="hidden md:inline-flex btn-secondary text-sm">Masuk</a>
                <a href="{{ route('login') }}" class="hidden md:inline-flex btn-primary text-sm gap-1.5 items-center">
                    Mulai Gratis
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-text-secondary hover:bg-surface-hover transition-colors" aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown menu --}}
        <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden pb-4 border-t border-border pt-4 mt-0 space-y-2">
            <a href="#fitur" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-text-secondary hover:bg-surface-hover transition-colors">Fitur</a>
            <a href="#cara-kerja" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-text-secondary hover:bg-surface-hover transition-colors">Cara Kerja</a>
            <a href="#tentang" @click="mobileOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-text-secondary hover:bg-surface-hover transition-colors">Tentang</a>
            <hr class="border-border my-3">
            <a href="{{ route('login') }}" @click="mobileOpen = false" class="block px-3 py-2 text-sm font-medium text-brand-600 hover:bg-surface-hover transition-colors">Masuk</a>
            <a href="{{ route('login') }}" @click="mobileOpen = false" class="block px-3 py-2.5 text-sm font-medium text-center text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors">Mulai Gratis →</a>
        </div>
    </div>
</nav>

{{-- ===== SECTION 2: HERO UTAMA ===== --}}
<section class="relative min-h-screen flex items-center overflow-hidden">
    {{-- Background decoration --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-brand-500/10 dark:bg-brand-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-brand-400/10 dark:bg-brand-400/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-brand-300/10 dark:bg-brand-300/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
        <div class="lg:grid lg:grid-cols-5 lg:gap-8 xl:gap-12 lg:items-center">
            {{-- LEFT COL: Headline + CTA + Badges --}}
            <div class="lg:col-span-3">
                {{-- 1. HEADLINE block --}}
                <div class="text-center lg:text-left max-w-4xl mx-auto lg:max-w-none mb-14 md:mb-18">
                    <h1 class="font-heading text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-text-primary leading-tight mb-6">
                        Pantau Harga Komoditas,<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-brand-400">Prediksi Tren Pasar</span>
                    </h1>
                    <p class="text-lg md:text-xl text-text-secondary max-w-2xl mx-auto lg:mx-0 mb-10 leading-relaxed">
                        Dashboard real-time untuk harga sembako, protein, dan bumbu dapur. Dilengkapi prediksi AI dan visualisasi data interaktif.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('dashboard') }}" class="btn-primary inline-flex items-center gap-2 px-8 py-3 text-base">
                            Lihat Dashboard →
                        </a>
                        <a href="#fitur" class="btn-secondary inline-flex items-center gap-2 px-8 py-3 text-base">
                            Pelajari Lebih Lanjut ↓
                        </a>
                    </div>
                </div>

                {{-- 2. TRUST BADGES row --}}
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 md:gap-4">
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 text-sm font-medium border border-brand-200 dark:border-brand-700/50">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        22 Unit Test Passed
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 text-sm font-medium border border-brand-200 dark:border-brand-700/50">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Real-time Updates
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 text-sm font-medium border border-brand-200 dark:border-brand-700/50">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        AI Predictions
                    </span>
                </div>
            </div>

            {{-- RIGHT COL: Hero Visual Card --}}
            <div class="lg:col-span-2 mt-12 lg:mt-0">
                <div class="max-w-4xl mx-auto lg:max-w-none">
                    <div class="card !p-0 overflow-hidden shadow-2xl shadow-brand-500/10 dark:shadow-brand-900/30">
                        {{-- Window chrome --}}
                        <div class="px-6 py-3.5 border-b border-border flex items-center justify-between bg-surface-secondary/50">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <span class="text-xs text-text-muted font-mono">dashboard preview</span>
                            <div class="w-14"></div>
                        </div>
                        {{-- Stat cards --}}
                        <div class="p-6 md:p-8 grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
                            <div class="float-1 rounded-2xl p-6 bg-gradient-to-br from-brand-500 to-brand-700 text-white text-center">
                                <p class="text-3xl md:text-4xl font-bold font-heading mb-1">7</p>
                                <p class="text-sm font-medium text-white/80">Komoditas</p>
                                <p class="text-xs text-white/60 mt-1">Utama</p>
                            </div>
                            <div class="float-2 rounded-2xl p-6 bg-gradient-to-br from-emerald-500 to-teal-700 text-white text-center">
                                <p class="text-3xl md:text-4xl font-bold font-heading mb-1">34</p>
                                <p class="text-sm font-medium text-white/80">Wilayah</p>
                                <p class="text-xs text-white/60 mt-1">Coverage</p>
                            </div>
                            <div class="float-3 rounded-2xl p-6 bg-gradient-to-br from-violet-500 to-purple-700 text-white text-center">
                                <p class="text-3xl md:text-4xl font-bold font-heading mb-1">95%</p>
                                <p class="text-sm font-medium text-white/80">Akurasi</p>
                                <p class="text-xs text-white/60 mt-1">Prediksi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== SECTION 3: FITUR UNGGULAN (id="fitur") ===== --}}
<section id="fitur" class="py-20 md:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-heading text-3xl md:text-4xl font-bold text-text-primary mb-4">Fitur Unggulan</h2>
            <p class="text-text-secondary max-w-xl mx-auto">Platform lengkap untuk monitoring dan analisis harga komoditas</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 md:auto-rows-fr">

            {{-- Card 1: Monitor Harga Real-time --}}
            <div class="card h-full hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
                <div class="w-12 h-12 rounded-xl bg-brand-100 dark:bg-brand-900/50 flex items-center justify-center mb-5 text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-xl font-heading font-semibold text-text-primary mb-3">Monitor Harga Real-time</h3>
                <p class="text-text-secondary text-sm leading-relaxed mb-4">Pantau perubahan harga komoditas secara langsung dengan visualisasi interaktif berbasis Chart.js.</p>
                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-700/50">Chart.js Powered</span>
            </div>

            {{-- Card 2: Prediksi Harga AI --}}
            <div class="card h-full hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
                <div class="w-12 h-12 rounded-xl bg-brand-100 dark:bg-brand-900/50 flex items-center justify-center mb-5 text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-xl font-heading font-semibold text-text-primary mb-3">Prediksi Harga AI</h3>
                <p class="text-text-secondary text-sm leading-relaxed mb-4">Dapatkan prediksi harga 7-30 hari ke depan menggunakan algoritma machine learning yang akurat.</p>
                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-700/50">ML Algorithm</span>
            </div>

            {{-- Card 3: Multi-Wilayah --}}
            <div class="card h-full hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
                <div class="w-12 h-12 rounded-xl bg-brand-100 dark:bg-brand-900/50 flex items-center justify-center mb-5 text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-heading font-semibold text-text-primary mb-3">Multi-Wilayah</h3>
                <p class="text-text-secondary text-sm leading-relaxed mb-4">Analisis perbandingan harga antar wilayah dengan coverage seluruh Indonesia dan data regional.</p>
                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-700/50">Regional Analytics</span>
            </div>
        </div>
    </div>
</section>

{{-- ===== SECTION 4: CARA KERJA (id="cara-kerja") ===== --}}
<section id="cara-kerja" class="py-20 md:py-28 bg-surface">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-heading text-3xl md:text-4xl font-bold text-text-primary mb-4">Cara Kerja</h2>
            <p class="text-text-secondary max-w-xl mx-auto">Tiga langkah mudah untuk mulai memonitor harga komoditas</p>
        </div>

        <div class="relative flex flex-col md:flex-row items-center justify-center gap-8 md:gap-4 lg:gap-8">
            {{-- Mobile vertical connector line --}}
            <div class="md:hidden absolute top-14 bottom-14 left-1/2 -translate-x-1/2 w-0.5 bg-gradient-to-b from-brand-400 via-brand-500 to-brand-600 rounded-full"></div>

            {{-- Step 1 --}}
            <div class="relative z-10 flex flex-col items-center text-center md:flex-1 px-4 py-2">
                <div class="w-16 h-16 rounded-full bg-brand-600 text-white flex items-center justify-center text-2xl font-bold font-heading shadow-lg mb-5 ring-4 ring-brand-100 dark:ring-brand-900/50">1</div>
                <h3 class="text-xl font-heading font-semibold text-text-primary mb-3">Input Data</h3>
                <p class="text-text-secondary leading-relaxed max-w-xs text-sm">Masukkan data harga komoditas harian dari berbagai wilayah</p>
            </div>

            {{-- Desktop connector arrow --}}
            <div class="hidden md:flex items-center justify-center w-10 lg:w-16 shrink-0">
                <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>

            {{-- Step 2 --}}
            <div class="relative z-10 flex flex-col items-center text-center md:flex-1 px-4 py-2">
                <div class="w-16 h-16 rounded-full bg-brand-600 text-white flex items-center justify-center text-2xl font-bold font-heading shadow-lg mb-5 ring-4 ring-brand-100 dark:ring-brand-900/50">2</div>
                <h3 class="text-xl font-heading font-semibold text-text-primary mb-3">Analisis</h3>
                <p class="text-text-secondary leading-relaxed max-w-xs text-sm">Sistem memproses dan memvisualisasikan tren secara otomatis</p>
            </div>

            {{-- Desktop connector arrow --}}
            <div class="hidden md:flex items-center justify-center w-10 lg:w-16 shrink-0">
                <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>

            {{-- Step 3 --}}
            <div class="relative z-10 flex flex-col items-center text-center md:flex-1 px-4 py-2">
                <div class="w-16 h-16 rounded-full bg-brand-600 text-white flex items-center justify-center text-2xl font-bold font-heading shadow-lg mb-5 ring-4 ring-brand-100 dark:ring-brand-900/50">3</div>
                <h3 class="text-xl font-heading font-semibold text-text-primary mb-3">Prediksi</h3>
                <p class="text-text-secondary leading-relaxed max-w-xs text-sm">Dapatkan prediksi harga 7-30 hari ke depan dengan AI</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== SECTION 5: STATS / SOCIAL PROOF ===== --}}
<section class="py-20 md:py-28 bg-gradient-to-br from-brand-600 via-brand-700 to-brand-800 relative overflow-hidden">
    {{-- Decorative blurs --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-10 left-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-center gap-12 md:gap-8 lg:gap-16">

            {{-- Stat 1 --}}
            <div class="text-center md:flex-1">
                <p class="font-heading text-5xl md:text-6xl font-bold text-white mb-2">7+</p>
                <p class="text-lg font-semibold text-white/90 mb-1">Komoditas Utama</p>
                <p class="text-sm text-white/60">Sembako, protein, bumbu dapur</p>
            </div>

            {{-- Stat 2 --}}
            <div class="text-center md:flex-1">
                <p class="font-heading text-5xl md:text-6xl font-bold text-white mb-2">34</p>
                <p class="text-lg font-semibold text-white/90 mb-1">Wilayah</p>
                <p class="text-sm text-white/60">Coverage seluruh Indonesia</p>
            </div>

            {{-- Stat 3 --}}
            <div class="text-center md:flex-1">
                <p class="font-heading text-5xl md:text-6xl font-bold text-white mb-2">95%</p>
                <p class="text-lg font-semibold text-white/90 mb-1">Akurasi</p>
                <p class="text-sm text-white/60">Prediksi 7 hari ke depan</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== SECTION 6: CTA PENUTUP ===== --}}
<section class="py-20 md:py-28 bg-gradient-to-br from-brand-50 to-brand-100 dark:from-brand-900/20 dark:to-brand-800/20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        {{-- 1. Headline --}}
        <h2 class="font-heading text-3xl md:text-4xl font-bold text-text-primary mb-4">Siap Mulai Memantau Harga?</h2>

        {{-- 2. Subtext --}}
        <p class="text-lg text-text-secondary mb-10">Gratis. Tanpa kartu kredit. Setup dalam 5 menit.</p>

        {{-- 3. Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-base font-semibold bg-white dark:bg-brand-600 text-brand-600 dark:text-white shadow-lg shadow-brand-500/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                Daftar Sekarang →
            </a>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl text-base font-semibold border-2 border-white dark:border-brand-500 text-brand-700 dark:text-brand-300 hover:bg-white/50 dark:hover:bg-brand-700/30 transition-all">
                Lihat Demo
            </a>
        </div>
    </div>
</section>

{{-- ===== FOOTER (id="tentang") ===== --}}
<footer id="tentang" class="bg-surface border-t border-border py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8">

            {{-- Column 1: Brand --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2.5">
                    <svg class="w-7 h-7 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                        <polyline points="17 6 23 6 23 12"/>
                    </svg>
                    <span class="text-lg font-heading font-bold text-brand-600">PriceWatch</span>
                </div>
                <p class="text-sm text-text-secondary leading-relaxed">Commodity Price Monitoring Dashboard</p>
                <p class="text-xs text-text-muted">&copy; {{ date('Y') }} PriceWatch. All rights reserved.</p>
            </div>

            {{-- Column 2: Links --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-text-primary uppercase tracking-wider">Navigasi</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('dashboard') }}" class="text-sm text-text-secondary hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Dashboard</a></li>
                    <li><a href="{{ route('predictions.index') }}" class="text-sm text-text-secondary hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Prediksi</a></li>
                    <li><a href="{{ route('commodities.index') }}" class="text-sm text-text-secondary hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Komoditas</a></li>
                    <li><a href="{{ route('regions.index') }}" class="text-sm text-text-secondary hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Wilayah</a></li>
                </ul>
            </div>

            {{-- Column 3: Tech Stack --}}
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-text-primary uppercase tracking-wider">Tech Stack</h4>
                <ul class="space-y-3">
                    <li class="text-sm text-text-secondary">Laravel</li>
                    <li class="text-sm text-text-secondary">Tailwind CSS v4</li>
                    <li class="text-sm text-text-secondary">Alpine.js</li>
                    <li class="text-sm text-text-secondary">Chart.js</li>
                </ul>
            </div>
        </div>
    </div>
</footer>

</body>
</html>

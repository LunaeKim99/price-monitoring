<nav class="bg-surface border-b border-border sticky top-0 z-50" x-data="{ mobileOpen: false }">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
        {{-- LEFT: Brand --}}
        <div class="flex items-center gap-2">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-text-primary hover:text-brand-600 transition-colors">
                <svg class="w-7 h-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span class="text-lg font-bold tracking-tight">PriceWatch</span>
            </a>
        </div>

        {{-- CENTER: Navigation links (desktop) --}}
        <div class="hidden md:flex items-center gap-1">
            <a href="{{ route('dashboard') }}"
               class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('dashboard') ? 'text-brand-600 font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Dashboard
            </a>
            <a href="{{ route('commodities.index') }}"
               class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('commodities.*') ? 'text-brand-600 font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Komoditas
            </a>
            <a href="{{ route('regions.index') }}"
               class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('regions.*') ? 'text-brand-600 font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Wilayah
            </a>
            <a href="{{ route('price-records.index') }}"
               class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('price-records.*') ? 'text-brand-600 font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Data Harga
            </a>
            <a href="{{ route('predictions.index') }}"
               class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('predictions.*') ? 'text-brand-600 font-semibold' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Prediksi
            </a>
        </div>

        {{-- RIGHT: Actions --}}
        <div class="flex items-center gap-3">
            {{-- Theme Toggle --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 rounded-lg text-text-secondary hover:bg-surface-hover transition-colors">
                    <svg x-show="!$store.theme.isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="$store.theme.isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-40 bg-surface border border-border rounded-xl shadow-lg py-1 z-50 dark:border-gray-700">
                    <template x-for="(opt, i) in ['light', 'dark', 'system']" :key="i">
                        <button @click="$store.theme.setTheme(opt); open = false" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-text-primary hover:bg-surface-hover transition-colors"
                                :class="{ 'text-brand-600 font-medium': $store.theme.theme === opt }">
                            <span x-text="opt.charAt(0).toUpperCase() + opt.slice(1)"></span>
                            <svg x-show="$store.theme.theme === opt" class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </button>
                    </template>
                </div>
            </div>

            @auth
                {{-- Avatar Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center text-sm font-medium hover:bg-brand-700 transition-colors">
                        {{ substr(auth()->user()?->name ?? 'A', 0, 1) }}
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-surface border border-border rounded-xl shadow-lg py-1 z-50 dark:border-gray-700">
                        <div class="px-4 py-2 text-sm text-text-primary font-medium truncate">{{ auth()->user()->name }}</div>
                        <hr class="border-border dark:border-gray-700">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-text-primary hover:bg-surface-hover transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard
                        </a>
                        <hr class="border-border dark:border-gray-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-surface-hover transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-500 dark:hover:text-brand-400 transition-colors">Login</a>
            @endauth

            {{-- Mobile Hamburger --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-text-secondary hover:bg-surface-hover transition-colors">
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu Dropdown --}}
    <div x-show="mobileOpen" @click.outside="mobileOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-border dark:border-gray-700 bg-surface">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('dashboard') ? 'text-brand-600 font-semibold bg-surface-hover' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Dashboard
            </a>
            <a href="{{ route('commodities.index') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('commodities.*') ? 'text-brand-600 font-semibold bg-surface-hover' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Komoditas
            </a>
            <a href="{{ route('regions.index') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('regions.*') ? 'text-brand-600 font-semibold bg-surface-hover' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Wilayah
            </a>
            <a href="{{ route('price-records.index') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('price-records.*') ? 'text-brand-600 font-semibold bg-surface-hover' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Data Harga
            </a>
            <a href="{{ route('predictions.index') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ Request::routeIs('predictions.*') ? 'text-brand-600 font-semibold bg-surface-hover' : 'text-text-secondary hover:text-text-primary hover:bg-surface-hover' }}">
                Prediksi
            </a>
        </div>
        @auth
            <div class="border-t border-border dark:border-gray-700 px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center text-sm font-medium flex-shrink-0">
                    {{ substr(auth()->user()?->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-text-primary truncate">{{ auth()->user()->name }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Logout</button>
                </form>
            </div>
        @else
            <div class="border-t border-border dark:border-gray-700 px-4 py-3">
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">Login</a>
            </div>
        @endauth
    </div>
</nav>

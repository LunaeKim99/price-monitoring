<nav class="bg-surface border-b border-border px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-text-primary">@yield('title', 'Dashboard')</h1>
    </div>
    <div class="flex items-center gap-4">
        @auth
            <!-- Theme Toggle -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="p-2 rounded-lg text-text-secondary hover:bg-surface-hover transition-colors">
                    <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                     class="absolute right-0 mt-2 w-40 bg-surface border border-border rounded-xl shadow-lg py-1 z-50">
                    <template x-for="(opt, i) in ['light', 'dark', 'system']" :key="i">
                        <button @click="setTheme(opt); open = false" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-text-primary hover:bg-surface-hover transition-colors"
                                :class="{ 'text-brand-600 font-medium': theme === opt }">
                            <span x-text="opt.charAt(0).toUpperCase() + opt.slice(1)"></span>
                            <svg x-show="theme === opt" class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Avatar -->
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
                     class="absolute right-0 mt-2 w-40 bg-surface border border-border rounded-xl shadow-lg py-1 z-50">
                    <div class="px-4 py-2 text-sm text-text-primary font-medium">{{ auth()->user()->name }}</div>
                    <hr class="border-border">
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
            <a href="{{ route('login') }}" class="text-sm text-brand-600 hover:text-brand-700">Login</a>
        @endauth
    </div>
</nav>

<nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <h1 class="text-lg font-semibold text-gray-800">
            @yield('title', 'Dashboard')
        </h1>
    </div>

    <div class="flex items-center gap-4">
        @auth
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                        Logout
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Login
            </a>
        @endauth
    </div>
</nav>

{{-- Header with user dropdown --}}
<header class="fixed top-0 left-0 right-0 z-30 border-b border-gray-200 bg-white lg:left-64">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        {{-- Mobile menu button (Hamburger) --}}
        <button 
            @click="sidebarOpen = true"
            type="button" 
            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 lg:hidden"
        >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Right side --}}
        <div class="flex items-center space-x-2 sm:space-x-4 ml-auto">
            {{-- PIN Balance --}}
            <div class="flex items-center space-x-2 rounded-lg bg-blue-50 px-2 py-2 sm:px-3">
                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs sm:text-sm font-medium text-blue-900">PIN: {{ auth()->user()->pin_point }}</span>
            </div>

            {{-- Wallet Balance --}}
            <div class="flex items-center space-x-2 rounded-lg bg-green-50 px-2 py-2 sm:px-3">
                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs sm:text-sm font-medium text-green-900">
                    <span class="hidden sm:inline">Rp </span>{{ number_format(auth()->user()->wallet->balance ?? 0, 0, ',', '.') }}
                </span>
            </div>

            {{-- User dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button 
                    type="button" 
                    @click="open = !open"
                    class="flex items-center space-x-2 sm:space-x-3 rounded-lg px-2 py-2 sm:px-3 text-gray-700 hover:bg-gray-100"
                >
                    <x-member-avatar :member="auth()->user()" size="sm" />
                    <div class="text-left hidden sm:block">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->id }}</p>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 hidden sm:block" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>

                {{-- Dropdown menu --}}
                <div 
                    x-show="open"
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    style="display: none;"
                >
                    {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a> --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

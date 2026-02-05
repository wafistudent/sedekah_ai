{{-- Sidebar Navigation --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed left-0 top-0 z-40 h-screen w-64 border-r border-gray-200 bg-white transition-transform duration-300 ease-in-out lg:translate-x-0">
    <div class="flex h-full flex-col overflow-y-auto px-3 py-4">
        {{-- Logo and Close Button --}}
        <div class="mb-10 flex items-center justify-between px-3">
            <div class="h-24">
                <img src="{{ asset('images/aisosial_word.webp') }}" alt="aisosisal" class=" object-">
            </div>
            {{-- Close button (mobile only) --}}
            <button @click="sidebarOpen = false" type="button"
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 lg:hidden">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Navigation Menu --}}
        <ul class="space-y-2 font-medium">
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1x 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                    </svg>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>

            {{-- Learning Materials  --}}
            <li>
                <a href="{{ route('admin.materials.index') }}"
                    class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.materials.*') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                    </svg>
                <span class="ml-3">Manage Produk</span>
                </a>
            </li>

            {{-- Member Routes --}}
            <li>
                <a href="{{ route('admin.members') }}"
                    class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('members.*') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    <span class="ml-3">Members</span>
                </a>
            </li>

            {{-- PIN Management --}}
            <li>
                <button type="button"
                    class="flex w-full items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100"
                    onclick="this.nextElementSibling.classList.toggle('hidden')">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                        <path fill-rule="evenodd"
                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3 flex-1 text-left">PIN Management</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <ul class="hidden space-y-2 py-2">
                    <li>
                        <a href="{{ route('pins.index') }}"
                            class="flex items-center rounded-lg py-2 pl-11 pr-3 text-gray-900 hover:bg-gray-100">
                            PIN History
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pins.purchase') }}"
                            class="flex items-center rounded-lg py-2 pl-11 pr-3 text-gray-900 hover:bg-gray-100">
                            Transfer PIN
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Marketing PINs --}}
            <li>
                <a href="{{ route('admin.marketing-pins.index') }}"
                    class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('admin.marketing-pins.*') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-3">Marketing PINs</span>
                </a>
            </li>

            {{-- Settings --}}
            <li>
                <button type="button"
                    class="flex w-full items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100"
                    onclick="this.nextElementSibling.classList.toggle('hidden')">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                        <path fill-rule="evenodd"
                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3 flex-1 text-left">Setting</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <ul class="hidden space-y-2 py-2">
                    <li>
                        <a href="{{ route('admin.settings.commission-config') }}"
                            class="flex items-center rounded-lg py-2 pl-11 pr-3 text-gray-900 hover:bg-gray-100">
                            Komisi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.app-settings') }}"
                            class="flex items-center rounded-lg py-2 pl-11 pr-3 text-gray-900 hover:bg-gray-100">
                            App Settings
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Withdrawals --}}
            <li>
                <a href="{{ route('admin.withdrawals') }}"
                    class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('withdrawals.*') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3">Withdrawals</span>
                </a>
            </li>

            {{-- Learning Materials (Members Only) --}}
            @if (!auth()->user()->hasRole('admin'))
                <li>
                    <a href="{{ route('materials.index') }}"
                        class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('materials.*') && !request()->routeIs('admin.materials.*') ? 'bg-gray-100' : '' }}">
                        <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                        </svg>
                        <span class="ml-3">Sumber Belajar</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</aside>

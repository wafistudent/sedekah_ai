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
                            d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                    </svg>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>

            {{-- Member Routes --}}
            <li class="{{ auth()->user()->hasRole('admin') ? 'hidden' : 'block' }}">
                <a href="{{ route('members.network-tree') }}"
                    class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('members.*') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    <span class="ml-3">Network Tree</span>
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
                        <a href="{{ route('pins.transfer') }}"
                            class="flex items-center rounded-lg py-2 pl-11 pr-3 text-gray-900 hover:bg-gray-100">
                            Transfer PIN
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pins.reedem') }}"
                            class="flex items-center rounded-lg py-2 pl-11 pr-3 text-gray-900 hover:bg-gray-100">
                            Redeem PIN
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Wallet --}}
            <li>
                <a href="{{ route('wallet.index') }}"
                    class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('wallet.*') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3">Wallet</span>
                </a>
            </li>

            {{-- Commission --}}
            {{-- <li>
                <a href="{{ route('commissions.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('commissions.*') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-3">Commission</span>
                </a>
            </li> --}}

            {{-- Withdrawals --}}
            <li>
                <a href="{{ route('withdrawals.my-requests') }}"
                    class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100 {{ request()->routeIs('withdrawals.*') ? 'bg-gray-100' : '' }}">
                    <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3">My Withdrawals</span>
                </a>
            </li>

            {{-- Admin Menu --}}
            @can('role:admin')
                <li class="pt-4 mt-4 border-t border-gray-200">
                    <p class="mb-2 px-3 text-xs font-semibold uppercase text-gray-500">Admin</p>
                </li>

                <li>
                    <a href="{{ route('admin.members') }}"
                        class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100">
                        <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                        <span class="ml-3">All Members</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.pins.purchase') }}"
                        class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100">
                        <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                            <path fill-rule="evenodd"
                                d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="ml-3">Purchase PIN</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.withdrawals') }}"
                        class="flex items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100">
                        <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="ml-3">Withdrawals</span>
                    </a>
                </li>

                <li>
                    <button type="button"
                        class="flex w-full items-center rounded-lg px-3 py-2.5 text-gray-900 hover:bg-gray-100"
                        onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="ml-3 flex-1 text-left">Settings</span>
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
                                Commission Config
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
            @endcan
        </ul>
    </div>
</aside>

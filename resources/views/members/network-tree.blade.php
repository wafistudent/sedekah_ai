@extends('layouts.app')

@section('title', 'Network Tree')

@section('content')
<div class="space-y-6" x-data="networkTreeComponent()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">Network Tree</h1>
            <p class="mt-1 text-sm text-gray-600">Your MLM network hierarchy</p>
        </div>
        <a href="{{ route('pins.reedem') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
            </svg>
            Register New Member
        </a>
    </div>

    {{-- Network Statistics --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-6">
        <x-stats-card title="Total Downlines" :value="$totalDownlines" color="blue" />
        <x-stats-card title="Active Downlines" :value="$activeDownlines" color="green" />
        <x-stats-card title="Deepest Level" :value="$deepestLevel" color="purple" />
        <x-stats-card title="Available Slots" :value="$availableSlots" color="orange" />
    </div>

    {{-- Network Tree Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Network Hierarchy (8 Levels)</h3>
            <p class="mt-1 text-sm text-gray-500">View your network structure by level</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                            Level
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Members
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($level = 1; $level <= 8; $level++)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-blue-100">
                                        <span class="text-sm font-medium text-blue-900">L{{ $level }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if(isset($networkTree[$level]) && count($networkTree[$level]) > 0)
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($networkTree[$level] as $member)
                                            <div class="flex items-center space-x-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                                <x-member-avatar :member="$member" size="sm" />
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                                    <p class="truncate text-xs text-gray-500">@{{ $member->id }}</p>
                                                </div>
                                                <div class="flex space-x-1">
                                                    <button 
                                                        @click="showMemberDetail('{{ $member->id }}')"
                                                        class="rounded p-1 text-blue-600 hover:bg-blue-100"
                                                        title="View Details"
                                                    >
                                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                    @if($level < 8)
                                                        <a 
                                                            href="{{ route('pins.reedem', ['upline' => $member->id]) }}"
                                                            class="rounded p-1 text-green-600 hover:bg-green-100"
                                                            title="Add Member Under"
                                                        >
                                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 italic">
                                        No members at this level yet
                                        @if($level == 1)
                                            - <a href="{{ route('pins.reedem') }}" class="text-blue-600 hover:text-blue-500">Register your first downline</a>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    {{-- Member Detail Modal --}}
    <div 
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showModal = false"
    >
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div 
                x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                @click="showModal = false"
            ></div>

            {{-- Modal panel --}}
            <div 
                x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
            >
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Member Details</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="member.name"></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Username</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="'@' + member.id"></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="member.email"></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="member.phone || '-'"></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Join Date</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="member.join_date"></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span 
                                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                    :class="member.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    x-text="member.status"
                                ></span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">PIN Balance</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="member.pin_point || 0"></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Wallet Balance</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(member.wallet_balance || 0)"></dd>
                        </div>
                    </dl>
                </div>

                <div class="mt-6 flex justify-end">
                    <button 
                        @click="showModal = false"
                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

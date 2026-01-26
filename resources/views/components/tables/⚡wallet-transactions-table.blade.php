<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\WalletTransaction;

new class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    
    #[Url]
    public string $sortField = 'created_at';
    
    #[Url]
    public string $sortDirection = 'desc';
    
    #[Url]
    public int $perPage = 25;

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when perPage changes
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Sort by the given field
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Clear search
     */
    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Get transactions with filters
     */
    public function getTransactionsProperty()
    {
        $query = WalletTransaction::where('user_id', auth()->id());

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('reference_type', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('components.tables.⚡wallet-transactions-table', [
            'transactions' => $this->transactions,
        ]);
    }
};
?>

<div class="bg-white rounded-lg shadow overflow-hidden">
    {{-- Search and Controls --}}
    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search transactions..." 
                class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
            @if($search)
                <button 
                    wire:click="clearSearch" 
                    type="button"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                >
                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>
        
        <div class="flex items-center gap-2">
            <label for="perPage" class="text-sm text-gray-700">Show:</label>
            <select 
                wire:model.live="perPage" 
                id="perPage"
                class="border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            >
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto" wire:loading.class="opacity-50">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th 
                        wire:click="sortBy('created_at')" 
                        scope="col" 
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                    >
                        <div class="flex items-center gap-1">
                            Date
                            @if($sortField === 'created_at')
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    @if($sortDirection === 'asc')
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    @else
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    @endif
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th 
                        wire:click="sortBy('amount')" 
                        scope="col" 
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                    >
                        <div class="flex items-center justify-end gap-1">
                            Amount
                            @if($sortField === 'amount')
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    @if($sortDirection === 'asc')
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    @else
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    @endif
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th 
                        wire:click="sortBy('after_balance')" 
                        scope="col" 
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                    >
                        <div class="flex items-center justify-end gap-1">
                            Balance
                            @if($sortField === 'after_balance')
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    @if($sortDirection === 'asc')
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    @else
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    @endif
                                </svg>
                            @endif
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{  \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                {{ $transaction->reference_type === 'commission' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $transaction->reference_type === 'withdrawal' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $transaction->reference_type === 'registration_fee' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $transaction->reference_type === 'adjustment' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $transaction->reference_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $transaction->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium
                            {{ $transaction->amount >= 0 ? 'text-green-600' : 'text-red-600' }}
                        ">
                            {{ $transaction->amount >= 0 ? '+' : '' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            Rp {{ number_format($transaction->after_balance, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm font-medium">No transactions found</p>
                                @if($search)
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your search</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center">
        <div class="flex items-center gap-2 text-blue-600">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium">Loading...</span>
        </div>
    </div>

    {{-- Pagination --}}
    @if($transactions->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $transactions->firstItem() }}</span> to <span class="font-medium">{{ $transactions->lastItem() }}</span> of <span class="font-medium">{{ $transactions->total() }}</span> results
                </div>
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

# Laravel Livewire Tables Documentation

## Overview

This project has been enhanced with **Laravel Livewire** to convert static HTML tables into dynamic, interactive tables with real-time search, sorting, and pagination capabilities. The implementation follows best practices and is fully compatible with the existing Tailwind CSS design system and Alpine.js functionality.

## What Has Been Implemented

### 1. Livewire Installation & Configuration

✅ **Laravel Livewire v4** has been installed via Composer
✅ Livewire styles and scripts have been integrated into the main layout (`resources/views/layouts/app.blade.php`)
✅ Configuration file published at `config/livewire.php`
✅ Fully compatible with existing Alpine.js setup

### 2. Three Interactive Table Components

Three reusable Livewire table components have been created using Livewire 4's single-file component (SFC) approach:

#### A. Wallet Transactions Table
**Location:** `resources/views/components/tables/⚡wallet-transactions-table.blade.php`
**Used In:** `resources/views/wallet/index.blade.php`

**Features:**
- Real-time search (debounced 300ms) across description and transaction type
- Sortable columns: Date, Amount, Balance
- Pagination with configurable options (10, 25, 50, 100 per page)
- Default: 25 records per page, sorted by Date DESC
- Shows transaction type badges (Commission, Withdrawal, Registration Fee, Adjustment)
- Color-coded amounts (green for credit, red for debit)
- Empty state with helpful message
- Loading indicator during data fetch

#### B. PIN Transactions Table
**Location:** `resources/views/components/tables/⚡pin-transactions-table.blade.php`
**Used In:** `resources/views/pins/index.blade.php`

**Features:**
- Real-time search across description, target, and type
- Sortable columns: Date, Points, Status
- Pagination with configurable options (10, 25, 50, 100 per page)
- Default: 25 records per page, sorted by Date DESC
- Type badges (Purchase, Transfer, Redeem)
- Status badges (Success, Failed)
- Color-coded points (green for positive, red for negative)
- Empty state with search suggestions
- Loading indicator

#### C. Commission Summary Table
**Location:** `resources/views/components/tables/⚡commission-summary-table.blade.php`
**Used In:** `resources/views/commissions/summary.blade.php`

**Features:**
- Real-time search by level number
- Sortable columns: Level, Total Amount, Transactions
- Pagination with configurable options (10, 25, 50, 100 per page)
- Default: 10 records per page, sorted by Level ASC
- Calculates average per transaction
- Shows percentage of total commission
- Summary row with totals
- Empty state for no commission data
- Loading indicator

### 3. Key Features Implemented

#### Search Functionality
- **Debounced input** (300ms) to reduce server requests
- **Clear search button** (X icon) appears when search term is active
- **Real-time filtering** using Livewire's `wire:model.live.debounce` directive
- **Resets pagination** automatically when search changes

#### Sorting
- **Clickable column headers** with hover state
- **Visual indicators** (up/down arrows) showing current sort direction
- **Toggle behavior** - clicking same column reverses sort direction
- **Persisted in URL** using Livewire's `#[Url]` attribute
- **Server-side sorting** for efficient handling of large datasets

#### Pagination
- **Tailwind-styled** pagination matching existing design
- **Configurable per-page** options: 10, 25, 50, 100
- **Shows record range** (e.g., "Showing 1 to 25 of 150 results")
- **Previous/Next buttons** with page numbers
- **Persisted in URL** for shareable links
- **Resets to page 1** when search or perPage changes

#### Loading States
- **Opacity overlay** on table during loading
- **Spinning icon** with "Loading..." text
- **Uses Livewire's wire:loading** directive
- **Smooth transitions** for better UX

#### Responsive Design
- **Horizontal scroll** on mobile for wide tables
- **Stacked layout** for search and per-page controls on mobile
- **Touch-friendly** interactive elements
- **Consistent spacing** across all screen sizes

### 4. Tailwind CSS Styling

All tables maintain consistent Tailwind styling:
- **Table headers:** `bg-gray-50 text-gray-500 uppercase text-xs font-medium`
- **Table rows:** `hover:bg-gray-50 transition-colors`
- **Borders:** `divide-y divide-gray-200`
- **Container:** `rounded-lg shadow overflow-hidden`
- **Search input:** Standard form input with focus states
- **Buttons:** `text-blue-600 hover:text-blue-500`

### 5. Performance Optimizations

- **Server-side processing:** All filtering, sorting, and pagination happen on the server
- **Efficient queries:** Using Eloquent's `paginate()` method
- **Debounced search:** Reduces database queries during typing
- **Proper indexing:** Database columns used for sorting/filtering should be indexed
- **Lazy loading:** Only loads current page of results

## How to Use

### Including a Livewire Table in a View

Simply use the Livewire component tag in your Blade template:

```blade
{{-- Wallet Transactions Table --}}
<livewire:tables.⚡wallet-transactions-table />

{{-- PIN Transactions Table --}}
<livewire:tables.⚡pin-transactions-table />

{{-- Commission Summary Table --}}
<livewire:tables.⚡commission-summary-table />
```

### Creating a New Livewire Table Component

Follow these steps to create a new interactive table:

#### Step 1: Create the Component

```bash
php artisan make:livewire Tables/YourTableName
```

This creates a single-file component at `resources/views/components/tables/⚡your-table-name.blade.php`

#### Step 2: Implement the PHP Logic

Add the following to the PHP section of your component:

```php
<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\YourModel;

new class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    
    #[Url]
    public string $sortField = 'created_at'; // default sort column
    
    #[Url]
    public string $sortDirection = 'desc'; // or 'asc'
    
    #[Url]
    public int $perPage = 25; // default records per page

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function getRecordsProperty()
    {
        $query = YourModel::query();

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('column1', 'like', '%' . $this->search . '%')
                  ->orWhere('column2', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('components.tables.⚡your-table-name', [
            'records' => $this->records,
        ]);
    }
};
?>
```

#### Step 3: Create the Blade Template

Use the provided templates as a reference and customize for your data structure. Key elements to include:

- Search input with clear button
- Per-page selector
- Table with sortable column headers
- Loading indicator
- Empty state
- Pagination component

## Technical Details

### Livewire Version
- **Livewire 4.0.3** (latest stable)
- Using single-file components (SFC) approach

### URL State Management
All filter states are persisted in the URL using Livewire's `#[Url]` attribute:
- `?search=term` - Current search term
- `?sortField=column` - Column being sorted
- `?sortDirection=asc|desc` - Sort direction
- `?perPage=25` - Records per page
- `?page=2` - Current page number

This allows:
- Shareable filtered views
- Browser back/forward navigation
- Bookmarkable table states

### Database Relationships

The components properly handle the database relationships:

**Wallet Transactions:**
- `WalletTransaction` → `Wallet` → `User`
- Queries are scoped to the authenticated user's wallet

**PIN Transactions:**
- `PinTransaction` → `User` (member_id)
- Queries are scoped to the authenticated user

**Commission Summary:**
- Aggregates `WalletTransaction` records by level
- Groups and sums commission amounts

## Testing

To test the Livewire tables:

1. Ensure database is migrated and seeded:
```bash
php artisan migrate --seed
```

2. Start the development server:
```bash
php artisan serve
```

3. Navigate to:
   - `/wallet` - Wallet Transactions Table
   - `/pins` - PIN Transactions Table
   - `/commissions/summary` - Commission Summary Table

4. Test features:
   - ✅ Type in search box (debounced)
   - ✅ Click column headers to sort
   - ✅ Change "Show X entries" dropdown
   - ✅ Click pagination buttons
   - ✅ Clear search with X button
   - ✅ View loading states
   - ✅ Test responsive design on mobile

## Benefits

### For Users
- ⚡ **Instant search** without page reloads
- 📊 **Easy data sorting** with one click
- 🔍 **Powerful filtering** across multiple columns
- 📱 **Mobile-friendly** responsive design
- 🔗 **Shareable URLs** preserve filter states
- 🎯 **Better UX** with loading indicators and empty states

### For Developers
- 🔧 **Reusable components** - Easy to create new tables
- 🎨 **Consistent styling** - Tailwind CSS integrated
- ⚙️ **Server-side processing** - Handles large datasets efficiently
- 🛠️ **Maintainable code** - Clean separation of concerns
- 📦 **Laravel-native** - No external JavaScript libraries required
- 🔄 **Real-time updates** - Livewire handles reactivity automatically

## Files Modified/Created

### Created:
- `config/livewire.php` - Livewire configuration
- `resources/views/components/tables/⚡wallet-transactions-table.blade.php`
- `resources/views/components/tables/⚡pin-transactions-table.blade.php`
- `resources/views/components/tables/⚡commission-summary-table.blade.php`

### Modified:
- `resources/views/layouts/app.blade.php` - Added Livewire scripts/styles
- `resources/views/wallet/index.blade.php` - Replaced static table
- `resources/views/pins/index.blade.php` - Replaced static table
- `resources/views/commissions/summary.blade.php` - Replaced static table
- `app/Http/Controllers/WalletController.php` - Removed pagination logic
- `app/Http/Controllers/PinController.php` - Removed pagination logic
- `app/Http/Controllers/CommissionController.php` - Updated for wallet relationship
- `composer.json` - Added Livewire dependency
- `composer.lock` - Updated dependencies

## Future Enhancements

Potential improvements for future iterations:

1. **Export functionality** - Add CSV/Excel export buttons
2. **Date range filtering** - Add date pickers for transaction filtering
3. **Bulk actions** - Select multiple rows for batch operations
4. **Column visibility** - Toggle which columns to display
5. **Saved filters** - Allow users to save common filter combinations
6. **Advanced filters** - More complex filter UI with multiple criteria
7. **Real-time updates** - Use Livewire polling for live data updates
8. **Table presets** - Quick filter buttons for common views

## Troubleshooting

### Common Issues

**Issue:** Table not loading
**Solution:** Ensure Livewire assets are published and included in layout

**Issue:** Search not working
**Solution:** Check database column names match the query in `getRecordsProperty()`

**Issue:** Sorting not working
**Solution:** Verify sortable column names exist in database table

**Issue:** Pagination shows wrong numbers
**Solution:** Check that query is using `paginate()` not `get()`

### Debug Mode

To enable Livewire debugging:

```php
// In config/livewire.php
'debug_mode' => env('APP_DEBUG', false),
```

## Conclusion

The Laravel Livewire tables implementation successfully transforms static HTML tables into dynamic, interactive components that provide an excellent user experience while maintaining performance and code quality. The implementation is production-ready and can easily be extended for additional tables throughout the application.

---

**Last Updated:** January 26, 2026
**Livewire Version:** 4.0.3
**Laravel Version:** 12.x

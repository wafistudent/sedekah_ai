<?php

use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\MarketingPinController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

require __DIR__ . '/auth.php';

Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Member routes
    Route::get('/members/network-tree', [MemberController::class, 'networkTree'])->name('members.network-tree');
    Route::get('/members/register', [MemberController::class, 'register'])->name('members.register');

    // PIN routes
    Route::prefix('pins')->name('pins.')->group(function () {
        Route::get('/', [PinController::class, 'index'])->name('index');
        Route::get('/transfer', [PinController::class, 'transfer'])->name('transfer');
        Route::post('/transfer', [PinController::class, 'storeTransfer'])->name('transfer.store');
        Route::get('/reedem', [PinController::class, 'reedem'])->name('reedem');
        Route::post('/reedem', [PinController::class, 'storeReedem'])->name('reedem.store');
    });

    Route::get('/qr-depth', [PinController::class, 'testing']);

    // Wallet routes
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/withdrawal', [WalletController::class, 'withdrawal'])->name('withdrawal');
        Route::post('/withdrawal', [WalletController::class, 'storeWithdrawal'])->name('withdrawal.store');
    });

    // Commission routes
    Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::get('/commissions/summary', [CommissionController::class, 'summary'])->name('commissions.summary');

    // My withdrawal requests
    Route::get('/withdrawals/my-requests', [WithdrawalController::class, 'myRequests'])->name('withdrawals.my-requests');

    // Material routes (Member side)
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{id}', [MaterialController::class, 'show'])->name('materials.show');
    Route::post('/materials/{id}/complete', [MaterialController::class, 'complete'])->name('materials.complete');

    Route::get('wa-health', [WhatsappController::class, 'index']);
    Route::get('wa-check', [WhatsappController::class, 'numberCheck']);

    // Admin routes (with role middleware)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/members', [AdminController::class, 'members'])->name('members');
        Route::get('/pins/purchase', [AdminController::class, 'purchasePinForm'])->name('pins.purchase');
        Route::post('/pins/purchase', [AdminController::class, 'storePurchasePin'])->name('pins.purchase.store');
        Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals');
        Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');
        Route::get('/settings/commission-config', [SettingController::class, 'commissionConfig'])->name('settings.commission-config');
        Route::post('/settings/commission-config', [SettingController::class, 'updateCommissionConfig'])->name('settings.commission-config.update');
        Route::get('/settings/app-settings', [SettingController::class, 'appSettings'])->name('settings.app-settings');
        Route::post('/settings/app-settings', [SettingController::class, 'updateAppSettings'])->name('settings.app-settings.update');
        
        // Material management routes
        Route::resource('materials', AdminMaterialController::class);
        
        // Marketing PIN Management
        Route::get('/marketing-pins', [MarketingPinController::class, 'index'])->name('marketing-pins.index');
        Route::get('/marketing-pins/create', [MarketingPinController::class, 'create'])->name('marketing-pins.create');
        Route::post('/marketing-pins', [MarketingPinController::class, 'store'])->name('marketing-pins.store');
        Route::get('/marketing-pins/{marketingPin}', [MarketingPinController::class, 'show'])->name('marketing-pins.show');
    });
});

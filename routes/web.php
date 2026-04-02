<?php

use App\Filament\Resources\Products\ProductResource;
use App\Http\Controllers\TransactionController;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('pos', function (Request $request) {

    $categoryId = $request->input('category');
    $search = $request->input('search');

    $query = ProductResource::getEloquentQuery()
        ->availableForSale()
        ->when($categoryId && $categoryId != 0, fn($q) => $q->where('category_id', $categoryId))
        ->when($search, fn($q) => $q->where('product_name', 'like', "%{$search}%"));

    $products = $query->paginate(16)->withQueryString();

    $categories = Category::query()
        ->where('is_active', true)
        ->whereHas('products', fn($q) => $q->availableForSale())
        ->orderBy('category_name')
        ->get();

    $customerSearch = $request->input('customer_search');

    $customers = Customer::query()
        ->when(
            $customerSearch,
            fn($q) =>
            $q->where('customer_name', 'like', "%{$customerSearch}%")
                ->orWhere('phone', 'like', "%{$customerSearch}%")
        )
        ->orderBy('customer_name')
        ->limit(5)
        ->get();

    $setting = Setting::current();

    return Inertia::render('Pos', [
        'categories' => $categories,
        'products' => $products,
        'customers' => $customers,
        'storeName' => $setting->store_name,
        'taxSettings' => [
            'gst_enabled'     => $setting->gst_enabled,
            'gst_percentage'  => (float) $setting->gst_percentage,
            'vat_enabled'     => $setting->vat_enabled,
            'vat_percentage'  => (float) $setting->vat_percentage,
            'currency_symbol' => $setting->currency_symbol,
        ],
    ]);
})->middleware(['auth'])->name('pos');

Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store')->middleware(['auth']);
Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt')->middleware(['auth']);

require __DIR__ . '/settings.php';

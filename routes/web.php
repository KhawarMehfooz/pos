<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Filament\Facades\Filament;
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

Route::get('pos',function(){
    return Inertia::render('Pos',[
        'categories' => Category::active()->get(),
        'products' => Product::availableForSale()->get(),
        'customers' => Customer::orderBy('customer_name')->limit(10)->get()
    ]);
})->middleware(['auth'])->name('pos');

require __DIR__.'/settings.php';

<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Basic Information')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('product_name')
                            ->label('Product Name')
                            ->placeholder('e.g. Apple iPhone 15 Pro')
                            ->prefixIcon('heroicon-o-tag')
                            ->required()
                            ->maxLength(255),

                        Select::make('category_id')
                            ->label('Product Category')
                            ->placeholder('Choose a category...')
                            ->prefixIcon('heroicon-o-folder')
                            ->options(
                                fn() => Category::where('is_active', true)
                                    ->pluck('category_name', 'id')
                            )
                            ->searchable()
                            ->nullable(),
                    ])->columnSpanFull(),

                Section::make('Pricing')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->schema([
                        TextInput::make('purchase_price')
                            ->label('Purchase Price')
                            ->placeholder('0.00')
                            ->prefixIcon('heroicon-o-banknotes')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('PKR')
                            ->required()
                            ->rules(['numeric', 'min:0'])
                            ->live()
                            ->columnSpan(1),

                        TextInput::make('retail_price')
                            ->label('Retail Price')
                            ->placeholder('0.00')
                            ->prefixIcon('heroicon-o-banknotes')
                            ->numeric()
                            ->suffix('PKR')
                            ->required()
                            ->live()
                            ->rules([
                                'numeric',
                                fn(Get $get) => function (string $attribute, $value, $fail) use ($get) {
                                    $purchase = $get('purchase_price');

                                    if ($purchase !== null && $value <= $purchase) {
                                        $fail('Retail price must be greater than purchase price.');
                                    }
                                },
                            ])
                            ->helperText('Must be higher than purchase price')
                            ->columnSpan(1),

                        TextInput::make('sale_price')
                            ->label('Sale / Discounted Price')
                            ->placeholder('0.00')
                            ->prefixIcon('heroicon-o-receipt-percent')
                            ->numeric()
                            ->suffix('PKR')
                            ->nullable()
                            ->live()
                            ->rules([
                                'nullable',
                                'numeric',
                                fn(Get $get) => function (string $attribute, $value, $fail) use ($get) {
                                    $retail = $get('retail_price');
                                    $purchase = $get('purchase_price');

                                    if ($retail !== null && $value > $retail) {
                                        $fail('Sale price cannot be higher than retail price.');
                                    }

                                    if ($purchase !== null && $value < $purchase) {
                                        $fail('Sale price cannot be lower than purchase price.');
                                    }
                                },
                            ])
                            ->helperText('Must be between purchase and retail price')
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),

                Section::make('Identifiers')
                    ->icon('heroicon-o-qr-code')
                    ->columns(2)
                    ->schema([
                        TextInput::make('barcode')
                            ->label('Barcode')
                            ->placeholder('e.g. 8901234567890')
                            ->prefixIcon('heroicon-o-bars-4')
                            ->numeric()
                            ->mask("999999999999999")
                            ->step(1)
                            ->minValue(0)
                            ->rules(['min:0'])
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->placeholder('e.g. APPL-IPH15-PRO-128')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->mask("999999999999999")
                            ->columnSpan(1),
                    ])->columnSpanFull(),

                Section::make('Stock Management')
                    ->icon('heroicon-o-archive-box')
                    ->columns(3)
                    ->schema([
                        Toggle::make('track_stock')
                            ->label('Track Stock')
                            ->default(true)
                            ->inline(false)
                            ->live()
                            ->onIcon('heroicon-o-eye')
                            ->offIcon('heroicon-o-eye-slash')
                            ->columnSpan(1),

                        TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->placeholder('0')
                            ->prefixIcon('heroicon-o-cube')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->live(onBlur: true)                        // <-- so min_stock_level can react
                            ->rules([
                                fn(Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if (! $get('track_stock')) return;  // skip when hidden

                                    if ((float) $value < 0) {
                                        $fail('Stock quantity cannot be negative.');
                                    }
                                },
                            ])
                            ->visible(fn(Get $get): bool => (bool) $get('track_stock'))
                            ->columnSpan(1),

                        TextInput::make('min_stock_level')
                            ->label('Minimum Stock Level')
                            ->placeholder('0')
                            ->prefixIcon('heroicon-o-exclamation-triangle')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->helperText('Must be less than or equal to current stock quantity.')
                            ->rules([
                                fn(Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if (! $get('track_stock')) return;  // skip when hidden

                                    $stock = (float) ($get('stock_quantity') ?? 0);
                                    $min   = (float) ($value ?? 0);

                                    if ($min > $stock) {
                                        $fail("Minimum stock level ({$min}) cannot be greater than current stock quantity ({$stock}).");
                                    }
                                },
                            ])
                            ->visible(fn(Get $get): bool => (bool) $get('track_stock'))
                            ->columnSpan(1),
                    ])->columnSpanFull(),

                Section::make('Media & Status')
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('product_image')
                            ->label('Product Image')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('product_images')
                            ->visibility('public')
                            ->nullable()
                            ->columnSpan(1),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle')
                            ->helperText('Inactive products won\'t appear in sales.')
                            ->columnSpan(1),
                    ])->columnSpanFull(),

            ]);
    }
}

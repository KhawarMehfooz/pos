<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make("product_image")
                    ->label("Product Image")
                    ->disk('public')
                    ->defaultImageUrl(url('/images/product-placeholder.jpeg'))
                    ->imageHeight(60),
                TextColumn::make("product_name")
                    ->label("Product Name")
                    ->searchable(),
                TextColumn::make('category.category_name')
                    ->label("Category"),
                TextColumn::make('retail_price')
                    ->label("Retail Price")
                    ->money('PKR'),

                TextColumn::make('sale_price')
                    ->label("Sale Price")
                    ->money('PKR')
                    ->default('N/A'),
                TextColumn::make('stock_quantity')
                    ->label('Stock Quantity')
                    ->badge()
                    ->formatStateUsing(
                        fn(Product $record) =>
                        $record->track_stock ? $record->stock_quantity : 'N/A'
                    )
                    ->color(function (Product $record): string {
                        if (! $record->track_stock) {
                            return 'gray';
                        }

                        return $record->stock_quantity <= $record->min_stock_level
                            ? 'danger'  
                            : 'success'; 
                    }),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

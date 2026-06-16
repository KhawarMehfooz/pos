<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('category_name')
                    ->label('Category Name')
                    ->prefixIcon('heroicon-o-tag')
                    ->placeholder('e.g. Beverages')
                    ->required()
                    ->maxLength(30),

                Select::make('parent_id')
                    ->label('Parent Category')
                    ->prefixIcon('heroicon-o-folder')
                    ->options(
                        fn () => Category::whereNull('parent_id')
                            ->pluck('category_name', 'id')
                    )
                    ->searchable()
                    ->placeholder('None (Top-level Category)')
                    ->nullable(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->inline(false)
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->helperText('Inactive categories and their products won\'t appear in the POS.'),
            ]);
    }
}

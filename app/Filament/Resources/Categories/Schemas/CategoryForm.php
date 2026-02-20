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
                    ->required()
                    ->label('Category Name')
                    ->maxLength(30),
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->options(
                        fn () => Category::whereNull('parent_id')
                            ->pluck('category_name', 'id')
                    )
                    ->searchable()
                    ->placeholder('None (Top-level Category)')
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}

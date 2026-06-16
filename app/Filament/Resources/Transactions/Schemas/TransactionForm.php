<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Order Details')
                    ->icon('heroicon-o-shopping-cart')
                    ->columns(3)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->prefixIcon('heroicon-o-user')
                            ->relationship('customer', 'customer_name')
                            ->searchable()
                            ->placeholder('Walk-in')
                            ->nullable()
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Status')
                            ->prefixIcon('heroicon-o-clock')
                            ->required()
                            ->options([
                                'hold'      => 'On Hold',
                                'completed' => 'Completed',
                            ])
                            ->default('hold')
                            ->columnSpan(1),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required()
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Card',
                            ])
                            ->default('cash')
                            ->columnSpan(1),
                    ]),

                Section::make('Financial Summary')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefixIcon('heroicon-o-calculator')
                            ->suffix('PKR')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        TextInput::make('discount')
                            ->label('Discount')
                            ->prefixIcon('heroicon-o-tag')
                            ->suffix('PKR')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->columnSpan(1),

                        TextInput::make('tax_amount')
                            ->label('Tax')
                            ->prefixIcon('heroicon-o-receipt-percent')
                            ->suffix('PKR')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        TextInput::make('grand_total')
                            ->label('Grand Total')
                            ->prefixIcon('heroicon-o-banknotes')
                            ->suffix('PKR')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        TextInput::make('paid_amount')
                            ->label('Paid Amount')
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->suffix('PKR')
                            ->numeric()
                            ->minValue(0)
                            ->columnSpan(1),

                        TextInput::make('change_amount')
                            ->label('Change Given')
                            ->prefixIcon('heroicon-o-arrow-uturn-left')
                            ->suffix('PKR')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ]),

            ]);
    }
}

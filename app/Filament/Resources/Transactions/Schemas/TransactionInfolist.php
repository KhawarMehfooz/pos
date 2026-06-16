<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Transaction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Order Details')
                    ->icon('heroicon-o-shopping-cart')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Transaction #')
                            ->icon('heroicon-o-hashtag')
                            ->formatStateUsing(fn ($state) => str_pad($state, 4, '0', STR_PAD_LEFT)),

                        TextEntry::make('customer.customer_name')
                            ->label('Customer')
                            ->icon('heroicon-o-user')
                            ->placeholder('Walk-in'),

                        TextEntry::make('created_at')
                            ->label('Date & Time')
                            ->icon('heroicon-o-calendar')
                            ->dateTime('M j, Y — g:i A'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->formatStateUsing(fn ($state) => ucfirst($state))
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'hold'      => 'warning',
                                default     => 'gray',
                            }),

                        TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->icon('heroicon-o-credit-card')
                            ->badge()
                            ->formatStateUsing(fn ($state) => ucfirst($state))
                            ->color(fn (string $state): string => match ($state) {
                                'cash' => 'success',
                                'card' => 'info',
                                default => 'gray',
                            }),
                    ]),

                Section::make('Financial Summary')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->icon('heroicon-o-calculator')
                            ->money('PKR'),

                        TextEntry::make('discount')
                            ->label('Discount')
                            ->icon('heroicon-o-tag')
                            ->money('PKR'),

                        TextEntry::make('tax_amount')
                            ->label('Tax')
                            ->icon('heroicon-o-receipt-percent')
                            ->money('PKR')
                            ->placeholder('—'),

                        TextEntry::make('grand_total')
                            ->label('Grand Total')
                            ->icon('heroicon-o-banknotes')
                            ->money('PKR')
                            ->weight('bold'),

                        TextEntry::make('paid_amount')
                            ->label('Paid Amount')
                            ->icon('heroicon-o-currency-dollar')
                            ->money('PKR')
                            ->placeholder('—'),

                        TextEntry::make('change_amount')
                            ->label('Change Given')
                            ->icon('heroicon-o-arrow-uturn-left')
                            ->money('PKR')
                            ->placeholder('—'),
                    ]),

                Section::make('Record Info')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->icon('heroicon-o-pencil')
                            ->dateTime('M j, Y — g:i A')
                            ->placeholder('—'),

                        TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->icon('heroicon-o-trash')
                            ->dateTime('M j, Y — g:i A')
                            ->visible(fn (Transaction $record): bool => $record->trashed())
                            ->placeholder('—'),
                    ]),

            ]);
    }
}

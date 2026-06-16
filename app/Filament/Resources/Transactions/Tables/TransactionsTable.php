<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Transaction')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->placeholder('Walk-in')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'hold',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->icon(fn ($state) => match ($state) {
                        'card' => 'heroicon-o-credit-card',
                        default => 'heroicon-o-banknotes',
                    })
                    ->colors([
                        'success' => 'cash',
                        'info'    => 'card',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('PKR')
                    ->sortable(),

                TextColumn::make('discount')
                    ->label('Discount')
                    ->money('PKR')
                    ->sortable(),

                TextColumn::make('grand_total')
                    ->label('Total')
                    ->money('PKR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money('PKR')
                    ->sortable(),

                TextColumn::make('change_amount')
                    ->label('Change')
                    ->money('PKR')
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('receipt')
                    ->label('Receipt')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->modalContent(fn(Transaction $record) => view('filament.receipt-modal', [
                        'url' => route('transactions.receipt', $record),
                    ]))
                    ->modalHeading(fn(Transaction $record) => 'Receipt #' . str_pad($record->id, 3, '0', STR_PAD_LEFT))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('lg'),
                // ViewAction::make(),
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

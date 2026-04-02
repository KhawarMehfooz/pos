<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SettingsPage extends Page
{
    protected string $view = 'filament-panels::pages.page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Settings';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::current()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Store Information')
                    ->description('These details appear on the printed receipt.')
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->columns(2)
                    ->schema([
                        TextInput::make('store_name')
                            ->label('Store Name')
                            ->prefixIcon(Heroicon::OutlinedBuildingStorefront)
                            ->required()
                            ->maxLength(100),

                        TextInput::make('store_address')
                            ->label('Address')
                            ->prefixIcon(Heroicon::OutlinedMapPin)
                            ->maxLength(255),

                        TextInput::make('store_phone')
                            ->label('Phone')
                            ->prefixIcon(Heroicon::OutlinedPhone)
                            ->tel()
                            ->mask("9999-9999999")
                            ->maxLength(30),

                        Select::make('currency_symbol')
                            ->label('Currency Symbol')
                            ->prefixIcon(Heroicon::OutlinedCurrencyDollar)
                            ->options([
                                '$'   => '$ — US Dollar',
                                '£'   => '£ — British Pound',
                                '€'   => '€ — Euro',
                                '¥'   => '¥ — Yen / Yuan',
                                '₹'   => '₹ — Indian Rupee',
                                'Rs'  => 'Rs — Pakistani Rupee',
                                'AED' => 'AED — UAE Dirham',
                                'SAR' => 'SAR — Saudi Riyal',
                            ])
                            ->required()
                            ->searchable(),
                    ]),

                Section::make('Tax')
                    ->description('Enable taxes to have them calculated and shown on receipts.')
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->columns(2)
                    ->schema([
                        Toggle::make('gst_enabled')
                            ->label('Enable GST')
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('gst_percentage')
                            ->label('GST %')
                            ->prefixIcon(Heroicon::OutlinedReceiptPercent)
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->visible(fn($get) => $get('gst_enabled'))
                            ->required(fn($get) => $get('gst_enabled')),

                        Toggle::make('vat_enabled')
                            ->label('Enable VAT')
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('vat_percentage')
                            ->label('VAT %')
                            ->prefixIcon(Heroicon::OutlinedReceiptPercent)
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->visible(fn($get) => $get('vat_enabled'))
                            ->required(fn($get) => $get('vat_enabled')),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions()),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::current()->fill($data)->save();

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}

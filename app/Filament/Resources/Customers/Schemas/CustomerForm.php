<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make("customer_name")
                    ->label("Customer Name")
                    ->prefixIcon('heroicon-m-user') 
                    ->required()
                    ->maxLength(30),

                TextInput::make("customer_phone")
                    ->label("Customer Phone")
                    ->prefixIcon('heroicon-m-phone') 
                    ->tel()
                    ->mask("9999-9999999")
                    ->unique(ignoreRecord: true), 

                TextInput::make("customer_email")
                    ->label("Customer Email")
                    ->prefixIcon('heroicon-m-envelope') 
                    ->email() 
                    ->unique(ignoreRecord: true), 
            ]);
    }
}

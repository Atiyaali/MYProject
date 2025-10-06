<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
      return $schema
            ->schema([

                Select::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->native(false),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

               TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255),

              TextInput::make('password')
                    ->same('password_confirmation')
                    ->password()
                    ->validationAttribute('password')
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
                TextInput::make('password_confirmation')->password(),
            ]);
    }
}

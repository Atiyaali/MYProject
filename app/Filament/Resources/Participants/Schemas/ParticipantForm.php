<?php

namespace App\Filament\Resources\Participants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('banner_image')
                ->image()
                ->directory('images')
                ->label('email_banner')
                ->disk('public'),
                TextInput::make('first_name'),

                TextInput::make('last_name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('Occupation'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('reg_key'),
                TextInput::make('auth_key'),
            ]);
    }
}

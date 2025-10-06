<?php

namespace App\Filament\Resources\Campaigns\Schemas;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
// use Illuminate\Support\Facades\Schema;
use Filament\Schemas\Components\Utilities\Set;

use App\Models\Participant;
use Filament\Forms\Components\Select;
class CampaignForm
{

    public static function configure(Schema $schema): Schema
    {
        $participantFields = \Illuminate\Support\Facades\Schema::getColumnListing((new Participant())->getTable());
        return $schema
            ->components([
                TextInput::make('name')
                    ->debounce(800)
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('path', "App\\Mail\\" . $state)),


                TextInput::make('path')
                    ->readOnly(),
                TextInput::make('subject'),
    //

                TextInput::make('cc'),
                TextInput::make('bcc'),

RichEditor::make('body')
    ->label('Email Body')
    ->required()
    ->fileAttachmentsDisk('public')
    ->fileAttachmentsDirectory('campaigns')
    ->extraAttributes(['id' => 'campaign-body-editor'])
    ->columnSpanFull(),
ViewField::make('placeholders')
                ->view('filament.placeholders', [
                    'fields' => $participantFields,
                ]),


            ]);
    }
}

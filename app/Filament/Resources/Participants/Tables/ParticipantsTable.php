<?php

namespace App\Filament\Resources\Participants\Tables;

use App\Models\Participant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ParticipantEDM;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;

use Filament\Forms\Components\FileUpload;
class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('Occupation')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('reg_key')
                    ->searchable(),
                TextColumn::make('auth_key')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
Action::make('previewQr')
    ->label('Preview QR')
    ->icon('heroicon-o-qr-code')
    ->color('primary')
    ->visible(fn ($record) => File::exists(public_path('qr/' . $record->auth_key . '.jpg')))
    ->modalHeading('QR Code Preview')
    ->modalWidth('md')
    ->modalContent(function ($record) {
        $url = asset('qr/' . $record->auth_key . '.jpg');
        return new HtmlString(
            "<div class='text-center'>
                 <img src='{$url}' alt='QR Code' class='mx-auto rounded shadow'>
             </div>"
        );
    })
    ->modalSubmitAction(false),

                Action::make('send_edm')
                    ->label('Send EDM')
                    // ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->action(function (Participant $record) {

                        // dd($record);
                        Mail::to($record->email)->send( new ParticipantEDM($record) );
                        Notification::make()
                            ->title("EDM sent to {$record->email}")
                            ->success()
                            ->send();
                    }),
            ])

         ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}

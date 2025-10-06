<?php

namespace App\Filament\Resources\ParticipantActivities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Filament\Resources\ParticipantActivityResource\Pages;
use App\Filament\Resources\ParticipantActivityResource\RelationManagers;
use App\Models\ParticipantActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParticipantActivitiesTable
{
    public static function configure(Table $table): Table
    {
          return $table
        ->columns([
            Tables\Columns\TextColumn::make('participant.first_name')
                ->label('Participant Name')
                ->searchable(),

            Tables\Columns\TextColumn::make('participant.email')
                ->label('Participant Email')
                ->searchable(),

            Tables\Columns\TextColumn::make('event')
                ->searchable(),


            Tables\Columns\TextColumn::make('type')
                ->searchable(),


            Tables\Columns\TextColumn::make('created_at')
                ->searchable(),

        ])
        ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
               ->recordActions([
                EditAction::make(),
                BulkActionGroup::make([
                DeleteBulkAction::make(),
                ]),

            ]);


    }
}

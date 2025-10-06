<?php

namespace App\Filament\Resources\ParticipantActivities;

use App\Filament\Resources\ParticipantActivities\Pages\CreateParticipantActivities;
use App\Filament\Resources\ParticipantActivities\Pages\EditParticipantActivities;
use App\Filament\Resources\ParticipantActivities\Pages\ListParticipantActivities;
use App\Filament\Resources\ParticipantActivities\Schemas\ParticipantActivitiesForm;
use App\Filament\Resources\ParticipantActivities\Tables\ParticipantActivitiesTable;
use App\Models\ParticipantActivity;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ParticipantActivitiesResource extends Resource
{
    protected static ?string $model = ParticipantActivity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Participants Management';
    protected static ?string $recordTitleAttribute = 'participant_activities';

    public static function form(Schema $schema): Schema
    {
        return ParticipantActivitiesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParticipantActivitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParticipantActivities::route('/'),
            'create' => CreateParticipantActivities::route('/create'),
            'edit' => EditParticipantActivities::route('/{record}/edit'),
        ];
    }
}

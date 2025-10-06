<?php

namespace App\Filament\Resources\ParticipantActivities\Pages;

use App\Filament\Resources\ParticipantActivities\ParticipantActivitiesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParticipantActivities extends ListRecords
{
    protected static string $resource = ParticipantActivitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

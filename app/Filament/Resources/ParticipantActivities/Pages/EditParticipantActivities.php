<?php

namespace App\Filament\Resources\ParticipantActivities\Pages;

use App\Filament\Resources\ParticipantActivities\ParticipantActivitiesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParticipantActivities extends EditRecord
{
    protected static string $resource = ParticipantActivitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

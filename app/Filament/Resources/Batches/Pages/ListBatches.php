<?php
namespace App\Filament\Resources\Batches\Pages;
use App\Filament\Resources\Batches\BatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Batches\Widgets\JobsCounter;
use Illuminate\Support\Facades\DB;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
class ListBatches extends ListRecords
{
    protected static string $resource = BatchResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            JobsCounter::class,
        ];
    }
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make(),
    //     ];
    // }

        protected function getHeaderActions(): array
    {
        return [
            // Existing Create action is automatically added.
            CreateAction::make(),
            Action::make('Truncate Jobs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => auth()->user()?->hasRole('super_admin'))
                ->modalHeading('Delete ALL jobs?')
                ->modalSubheading('This will permanently remove all rows from the jobs table.')
                ->modalButton('Yes, delete all')
                ->action(function () {
                activity()
                        ->event('deleted')
                        ->log('Delete jobs '.auth()->user()->id .' Participants');
                    DB::table('jobs')->truncate();
                    Notification::make()
                        ->title('All jobs deleted')
                        ->success()
                        ->send();
                }),
        ];
    }
}

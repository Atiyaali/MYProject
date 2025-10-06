<?php

namespace App\Filament\Resources\Batches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\BatchParticipant;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BatchParticipantExport;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use App\Jobs\SendBatchEmailJob;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;

class BatchesTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('compain.name')
                    ->searchable(),

               TextColumn::make('name')
                    ->searchable(),

            TextColumn::make('file')
                    ->badge()
                    ->default(fn($record) => class_exists($record->compain->path) == true ? "Exists" : "Not Exist"),
               TextColumn::make('participants_count')
                ->label('Participants Count')
                ->counts('participants')
                ->sortable()
                ->badge()
                ->color('info'),
            TextColumn::make('remarks')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),


    Action::make('BlastEdm')
                    ->visible(fn($record) =>
    // edmExists($record) &&
    BatchParticipant::where('batch_id', $record->id)->exists()
)

                    ->color('success')
                    ->icon('heroicon-o-envelope')
                    ->modalWidth('sm')
                    ->form([

                        Radio::make('status')
                            ->options([
                                'all' => 'Send to all',
                                'update-only' => 'Send to updated only',

                            ])
                            ->required(),
                    Forms\Components\TextInput::make('email')
                    ->label('Email Address'),

                            CheckboxList::make('cc')
                            ->options(function ($record) {
                                if ($record->compain->cc) {
                                    $ccEmails = explode(',', $record->compain->cc);
                                    return array_combine($ccEmails, $ccEmails);
                                }
                                return [];
                            })
                            ->label('CC Recipients'),


                            CheckboxList::make('bcc')
                            ->options(function ($record) {
                                if($record->compain->bcc) {
                                    $bccEmails = explode(',', $record->compain->bcc);
                                    return array_combine($bccEmails, $bccEmails);

                                }
                                return [];
                            })
                            ->label('BCC Recipients'),

                    ])
                    ->action(function ($record, array $data) {
                        static::sendEdm($record, $data);
                    }),

                // Batch PArticipants
        Action::make('AddBatchsData')
                    ->icon('heroicon-o-numbered-list')
                    ->url(fn($record) => '/admin/batch-participant?batch=' . $record->id),


        Action::make('export')
                    ->visible(fn($record)  => BatchParticipant::where('batch_id', $record->id)->count() > 0)
                    ->icon('heroicon-o-arrow-up-tray')
                    ->action(function ($record) {
                        activity()
                        ->event('Export Batch # '. $record->id)
                        ->log('Exporting Batch '. $record->id .' Participants');
                        return Excel::download(new BatchParticipantExport(BatchParticipant::where('batch_id', $record->id)->get()),  $record->name . '(' . Carbon::now()->format('Y-m-d H_i') . '.csv');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
  public static function sendEdm($record, $data)
    {

        if (class_exists($record->compain->path)) {

            if ($data['status'] == 'all') {
                $batchPaxs = BatchParticipant::where('batch_id', $record->id)->get();
            } else {
                $batchPaxs = BatchParticipant::where('batch_id', $record->id)->whereNull('sent_at')->get();
            }

            if($batchPaxs->count() > 0) {
                // dd($data['email']);
                foreach ($batchPaxs as $batch) {

                    SendBatchEmailJob::dispatch($batch, $data['cc'] ?? [],$data['email'] ?? $batch->participant->email,$data['bcc'] ?? []);

                }

                activity()
                ->performedOn($record)
                ->causedBy(auth()->user())
                ->event('Hit blast route')
                ->tap(function (Activity $activity) {
                    $activity->properties = (['ip' => request()->ip(), 'user-agent' => request()->userAgent()]);
                })
                ->log('Blast '. $record->compain->name);

                Notification::make()
                    ->title($record->compain->name . ' sent to the queue')
                    ->success()
                    ->send();

            }
            else{

                Notification::make()
                ->title('Batch is empty')
                ->danger()
                ->send();
            }
        }
    }



}

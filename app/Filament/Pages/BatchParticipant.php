<?php

namespace App\Filament\Pages;

use App\Exports\BatchParticipantExport;
use App\Imports\ParticipantBatchImport;
use App\Models\BatchParticipant as ModelsBatchParticipant;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\SelectFilter;
use Spatie\Activitylog\Contracts\Activity;
use Filament\Tables\Filters\Filter;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Traits\HasRoles;

class BatchParticipant extends Page implements HasTable
{
    use WithFileUploads;
    use InteractsWithTable , HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Email Management';
    protected string $view = 'filament.pages.batch-participant';

    // protected static ?string $navigationGroup = 'Email Management';

    protected static ?int $navigationSort = 3;

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString("<small>Displaying Batch Data with Participant Details</small>");
    }

    public function getModel(): string
    {
        return ModelsBatchParticipant::class;
    }

    public $batch;
    public $file;

    // public static function canAccess(): bool
    // {
    //     return auth()->user()?->hasRole('super_admin') ?? false;
    // }

    public function mount()
    {
        $this->batch = request()->batch;
    }
    public function import()
    {
        $this->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx']
        ]);

        Excel::import(new ParticipantBatchImport($this->batch), $this->file);

        Notification::make()
            ->title('Batch participants import successfully.')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->batch == null ? ModelsBatchParticipant::query() : ModelsBatchParticipant::query()->where('batch_id', $this->batch))
            ->columns([

                TextColumn::make('batch.name')
                    ->searchable(),

                TextColumn::make('compain.name')
                    ->label('Email')
                    ->searchable(),

                // TextColumn::make('participant.first_name')
                //     ->label('Participant Name')
                //     ->searchable(),
TextColumn::make('participant_display_name')
    ->label('Participant Name')
    ->searchable()
    ->getStateUsing(function ($record) {
        $participant = $record->participant;
        if (isset($participant->name) && $participant->name) {
            return $participant->name;
        }
        if (isset($participant->first_name) && $participant->first_name) {
            return $participant->first_name;
        }
        return '-';
    }),

                TextColumn::make('participant.email')
                    ->getStateUsing(function (ModelsBatchParticipant $record) {
                        if ($record->batch_id !== null) {
                            return $record->participant->email;
                        } else {
                            // preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $record->description, $email);
                            return $record->participant->email;
                        }
                    })
                    ->label('Participant Email')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->searchable(),

                TextColumn::make('sent_at')
                    ->label('Sent Edm At')
                    ->limit(20),

                TextColumn::make('description')
                    // ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('send again')
                    ->visible(fn($record) => $record->status !== 'pending' && $record->batch_id !== null)
                    ->icon('heroicon-o-arrow-uturn-right')
                    ->requiresConfirmation()
                    ->form([
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
                                if ($record->compain->bcc) {
                                    $bccEmails = explode(',', $record->compain->bcc);
                                    return array_combine($bccEmails, $bccEmails);
                                }
                                return [];
                            })
                            ->label('BCC Recipients'),

                    ])
                    ->action(function ($record, array $data) {
                        static::sendEdmAgain($record, $data);
                    }),
            ])
              ->filters([
            SelectFilter::make('batch_id')
                ->label('Batch Name')
                ->relationship('batch', 'name')
                // OR use options() if you want full control:
                // ->options(Batch::pluck('name', 'id'))
                ->searchable(),
       Filter::make('not_pending')
        ->label('Status ≠ Pending')
        ->query(fn ($query) => $query->where('status', '!=', 'pending')),

       Filter::make('not_sent')
        ->label('Status ≠ Sent')
        ->query(fn ($query) => $query->where('status', '!=', 'sent')),
//     Filter::make('status_pending')
//         ->label('Status = Pending')
//         ->query(fn ($query) => $query->where('status', 'pending')),

//    Filter::make('status_sent')
//         ->label('Status = Sent')
//         ->query(fn ($query) => $query->where('status', 'sent')),
        ])
            ->headerActions([
                Action::make('export')
                    ->label('Export All Batchs Data')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->action(function () {
                        activity()
                            ->causedBy(auth()->user())
                            ->event('Export')
                            ->withProperties(['key' => 'value'])
                            ->log('Export Batchs');
                        return Excel::download(new BatchParticipantExport(ModelsBatchParticipant::get()),  'all_batch_pax(' . Carbon::now()->format('Y-m-d H_i') . '.csv', \Maatwebsite\Excel\Excel::CSV);
                    }),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->action(fn(Collection $records) => $records->each->delete())
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
            ]);
    }

    public static function sendEdmAgain($record, $data)
    {

        $mail = $record->compain->path;
        Setting::configureSmtp();
        Mail::to($record->participant->email)
            ->cc($data['cc'])
            ->bcc($data['bcc'])
            ->send(new $mail($record));

        $sentDate = $record->sent_at == null ? now() : $record->sent_at . ', ' . now();

        $record->update([

            'status' => "sent",
            'sent_at' => $sentDate,
        ]);

        // activity()
        //     ->performedOn($record)
        //     ->causedBy(auth()->user())
        //     ->event('send Edm Again')
        //     ->tap(function (Activity $activity) {
        //         $activity->properties = (['ip' => request()->ip(), 'user-agent' => request()->userAgent()]);
        //     })
        //     ->log('Send Edm to ' . $record->participant->email);

        Notification::make()
            ->title('Email sent again.')
            ->success()
            ->send();
    }
}

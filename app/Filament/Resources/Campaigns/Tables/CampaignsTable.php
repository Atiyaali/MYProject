<?php

namespace App\Filament\Resources\Campaigns\Tables;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Participant;
use App\Mail\CampaignMail;
use App\Models\Campaign;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Exception;
use App\Models\BatchParticipant;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Spatie\Activitylog\Models\Activity;
class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('path')->searchable(),
                TextColumn::make('subject')->searchable(),
                TextColumn::make('cc')->searchable(),
                TextColumn::make('bcc')->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
 ->recordActions([
EditAction::make(),
DeleteAction::make(),
Action::make('Preview')
    ->icon('heroicon-o-eye')
    ->url(fn ($record) => route('campaign.preview', $record))
    ->openUrlInNewTab(),
// Action::make('Preview')
//     ->icon('heroicon-o-eye')
//     ->button()
//     ->modalHeading('Campaign Preview')
//     ->modalContent(function ($record) {
//         $participant = Participant::first();
//         $body = $record->body;

//         // Replace placeholders with participant values
//         if ($participant) {
//             foreach (Schema::getColumnListing($participant->getTable()) as $column) {
//                 $placeholder = '{' . $column . '}';
//                 $value = $participant->$column ?? '';
//                 $body = str_replace($placeholder, $value, $body);
//             }
//         }

//         // Fix image src paths
//         $body = preg_replace_callback(
//             '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
//             function ($matches) {
//                 $src = $matches[1];

//                 // If already absolute URL, keep it
//                 if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
//                     return '<img src="' . $src . '">';
//                 }

//                 // Normalize local storage paths
//                 $normalized = str_replace('storage/app/public', 'storage', $src);

//                 return '<img src="' . asset($normalized) . '">';
//             },
//             $body
//         );

//         return view('filament.campaign-preview', [
//             'subject' => $record->subject,
//             'body'    => $body,
//         ]);
//     })
//     ->modalSubmitAction(false),


Action::make('TestEdm')
        ->visible(fn ($record) => ! empty($record->body))
        ->color('success')
        ->icon('heroicon-o-envelope')
        ->modalWidth('sm')
        ->form([
TagsInput::make('emails')
     ->label('Emails')
    ->placeholder('Type email and press enter')
    ->separator(',')
    ->required()
    ->rule('array')
    ->nestedRecursiveRules([
        'email',
    ]),
Select::make('participant_id')
                ->label('Select Participant')
                ->options(
                    fn () => Participant::whereNotNull('first_name')
                        ->pluck('first_name', 'id')
                        ->toArray()
                )
                ->preload()
                ->required()
                ->searchable()
                ->native(false)
                ->default(fn () => Participant::whereNotNull('first_name')->first()?->id),
        ])

->action(function ($record, array $data) {
                        // multiple emails + BatchParticipant + SMTP + activity log
                        if (! class_exists($record->path)) {
                            return;
                        }

                        try {
                            $emails = is_array($data['emails'])
                                ? $data['emails']
                                : explode(',', $data['emails']);

                            foreach ($emails as $email) {
                                $batch = new BatchParticipant();
                                $batch->batch_id       = null;
                                $batch->participant_id = $data['participant_id'];
                                $batch->description    = "Test {$record->name} Edm sent to {$email}";
                                $batch->compain_id    = $record->id;
                                $batch->save();
                                $mailClass = $record->path;

                                Setting::configureSmtp();

                                Mail::to(trim($email))->send(new $mailClass($batch));

                                $batch->status  = "sent";
                                $batch->sent_at = now();
                                $batch->save();

                                activity()
                                    ->performedOn($record)
                                    ->causedBy(auth()->user())
                                    ->event('test Edm')
                                    ->tap(function (Activity $activity) {
                                        $activity->properties = [
                                            'ip' => request()->ip(),
                                            'user-agent' => request()->userAgent(),
                                        ];
                                    })
                                    ->log("Send Edm to {$email}");
                            }

                            Notification::make()
                                ->title($record->name . ' sent to selected emails')
                                ->success()
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
//         ->action(function ($record, array $data) {
//             $participant = Participant::find($data['participant_id']);
// if ($participant && !empty($data['emails'])) {
//     // dd($data['emails'], $participant);
//     $emails = explode(',', $data['emails']);
//     // dd($emails);
//     foreach ($emails as $email) {
//         // dd($email);

//         Mail::to($email)->queue(
//             new \App\Mail\CampaignMail(
//                 $participant,
//                 $record->subject,
//                 $record->body
//             )
//         );

//     }
// }

//         })
//         ->successNotificationTitle('EDM sent successfully!'),
// ])

            ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);

    }
}

<?php

namespace App\Filament\Resources\Participants\Pages;

use App\Filament\Resources\Participants\ParticipantResource;
use App\Imports\ParticipantImport;
use App\Models\Participant;
use Filament\Actions;
use Illuminate\Support\Facades\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
class ListParticipants extends ListRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {

        return [
            Actions\Action::make('Generate QR Codes')
            // ->icon('heroicon-o-qr-code')
            ->color('success')
            ->requiresConfirmation()
            ->action(function () {
                $qrDir = public_path('qr');
                if (!File::exists($qrDir)) {
                    File::makeDirectory($qrDir, 0755, true);
                }

                Participant::chunk(50, function ($participants) use ($qrDir) {
                    foreach ($participants as $participant) {
                        $path = $qrDir . '/' . $participant->auth_key . '.jpg';


                        if (File::exists($path)) {
                            continue;
                        }


                        $url = "https://api.qrserver.com/v1/create-qr-code/?data={$participant->auth_key}&size=300x300&ecc=L&bgcolor=fff&color=000000&margin=15";
                        $image = Http::get($url)->body();
                        File::put($path, $image);
                    }
                });
            }),

            // === DOWNLOAD SAMPLE CSV =========================================
            Actions\Action::make('SampleFile')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action(fn () => response()->download(
                    public_path('sample_files/participant-sample.csv')
                )),

            // === CREATE NEW PARTICIPANT ======================================
            Actions\CreateAction::make()
                ->after(function ($record) {
                    // activity()->performedOn($record)->log('Created a new participant');
                }),

            // === IMPORT PARTICIPANTS (Excel / CSV) ===========================
            Actions\Action::make('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    FileUpload::make('file')
                        ->label('Excel/CSV File')
                        ->required()
                        ->acceptedFileTypes([
                            'text/csv',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]),
                ])
                ->action(function (array $data) {
                    Excel::import(new ParticipantImport, $data['file']);
                }),

            // === EXPORT PARTICIPANTS =========================================
             ExportAction::make('Export')
                ->icon('heroicon-o-arrow-up-tray')
  ->exports([
        ExcelExport::make()
            ->fromModel(

                Participant::query()->distinct()
            )
            ->withFilename(fn () => 'participants-' . now()->format('Y-m-d'))
            ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
           ->withColumns(
    collect((new Participant)->getFillable())
        ->map(fn ($col) => Column::make($col))
        ->all()
)
    ])

                ->after(function () {
                    // activity()->event('Export')->log('Exported participants');
                }),
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString(
            '<small>Manage Participants: Import, export, and create new participants</small>'
        );
    }
}

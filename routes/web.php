<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Models\Campaign;
use App\Models\Participant;
use Illuminate\Support\Facades\Schema;
Route::get('/', function () {
    return view('welcome');
});




Route::get('/logs', [LogController::class, 'index'])->name('error_logs.index');
Route::get('/error-logs/{id}', [LogController::class, 'show'])->name('error_logs.show');

Route::get('/campaign-preview/{campaign}', function (Campaign $campaign) {
    $participant = Participant::first();
    $body = $campaign->body;

    if ($participant) {
        foreach (Schema::getColumnListing($participant->getTable()) as $column) {
            $body = str_replace(
                '{'.$column.'}',
                $participant->$column ?? '',
                $body
            );
        }
    }

    $body = preg_replace_callback(
        '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
        fn($m) => '<img src="' .
            (str_starts_with($m[1], 'http')
                ? $m[1]
                : asset(str_replace('storage/app/public', 'storage', $m[1]))
            ) . '">',
        $body
    );

    return view('filament.campaign-preview', [
        'subject' => $campaign->subject,
        'body'    => $body,
    ]);
})->name('campaign.preview');

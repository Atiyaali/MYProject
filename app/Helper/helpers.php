<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\OnsiteParticipantResource;


function storeImageFromUrl($url, $path)
{
    $image = file_get_contents($url);
    $image = imagecreatefromstring($image);
    $image = imagejpeg($image, public_path() . "{$path}");
    return $image;
}


function edmExists($record)
{
    $path = $record->path ?? null;
    return class_exists($path ?? $record->compain->path);
}

if (!function_exists('qrDirectoryExists')) {

    function qrDirectoryExists($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}


if (!function_exists('syncToOnsite')) {

    function syncToOnsite($pax)
    {
        $onsite_key = env('ONSITE_SK');
        $participant = new OnsiteParticipantResource($pax);
        $api_url = "https://backstream.klobbiapp.com/api/participants/single-sync";

        // Convert the resource to an array if needed.
        // Depending on your resource, you can use ->resolve() or ->toArray(request())
        $participantData = $participant->resolve();

        $response = Http::post($api_url, [
            'secret_key'  => $onsite_key,
            'participant' => $participantData,
        ]);

        if ($response->successful()) {
            Log::info('Participant synced to Onsite ' . $pax->id);
            // Handle a successful response.
        } else {
            // Handle errors.
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    public function store(Request $request)
    {

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|email|unique:participants,email',
            'phone'        => 'nullable|string|max:20',
            'Occupation'   => 'nullable|string|max:255',
            // 'banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')
                ->store('participants/banners', 'public');
        }

        $validated['reg_key']  = Str::uuid()->toString();
        $validated['auth_key'] = Str::random(40);


        $participant = Participant::create($validated);
        qrDirectoryExists(public_path('qr'));

        if (!file_exists(public_path() . "/qr/{$participant->auth_key}.jpg")) {
            storeImageFromUrl("https://api.qrserver.com/v1/create-qr-code/?data={$participant->auth_key}&size=300x300&ecc=H&qzone=2&bgcolor=fff&color=000000&margin=15", "/qr/{$participant->auth_key}.jpg");
        }
        $this->confirmationedm($participant);
        return response()->json([
            'message' => 'Participant stored successfully!',
            'data'    => $participant,
        ], 201);
    }
    function confirmationedm($participant){
        //
    }
}

<?php
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

Route::post('/participants', [ParticipantController::class, 'store']);
Route::get('/get_settings', [SettingsController::class, 'getSettings']);

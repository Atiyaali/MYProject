<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ParticipantActivity extends Model
{
    use HasFactory;

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public $dates = ['time'];



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

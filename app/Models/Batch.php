<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Batch extends Model
{
    use HasFactory , LogsActivity;

    protected $fillable = ['name', 'remarks', 'compain_id'];

    public function compain()
    {
        return $this->belongsTo(Campaign::class);
    }


    public function participants()
    {
        return $this->belongsToMany(Participant::class)
                    ->using(BatchParticipant::class)
                    ->withPivot('sent_emd_date', 'status')
                    ->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Participant extends Model
{
       use HasFactory , LogsActivity;

    protected $fillable = [
      'first_name',
        'last_name',
        'email',
        'phone',
        'Occupation',
        'banner_image',
        'reg_key',
        'auth_key',
    ];
     public static $step = [
     'first_name',
        'last_name',
        'email',
        'phone',
        'Occupation',
        // 'banner_image',
        // 'reg_key',
        // 'auth_key',
    ];
     public static $onsiteColumns = [
       'first_name',
        'last_name',
        'email',
        'phone',
        'Occupation',
        'banner_image',
        'reg_key',
        'auth_key',
    ];


      public function batches()
    {
        return $this->belongsToMany(Batch::class)
            ->using(BatchParticipant::class)
            ->withPivot('sent_emd_date', 'status')
            ->withTimestamps();
    }
  public function campaign()
{
    return $this->belongsTo(Campaign::class);
}

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
       protected static function booted()
    {
        static::creating(function ($participant) {
            // Only set if not manually provided
            if (empty($participant->reg_key)) {
                $participant->reg_key = Str::random(16);
            }

            if (empty($participant->auth_key)) {
                $participant->auth_key = Str::random(16);
            }
        });
    }

}

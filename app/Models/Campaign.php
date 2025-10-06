<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Campaign extends Model
{
    use HasFactory , LogsActivity;

    protected $fillable = [
        'name',
        'path',
        'subject',
        'cc',
        'bcc',
        'body',
        'file',
    ];
public function batches()
    {
        return $this->hasMany(Batch::class);
    }

  public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

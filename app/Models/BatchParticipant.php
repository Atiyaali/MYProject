<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BatchParticipant extends Pivot
{

    use LogsActivity;
    protected $table = 'batch_participant';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';


    protected $fillable = ['batch_id', 'participant_id', 'compain_id', 'sent_at', 'status', 'description'];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function compain()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

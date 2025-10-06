<?php

namespace App\Imports;

use App\Models\Batch;
use App\Models\BatchParticipant;
use App\Models\Participant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantBatchImport implements ToModel, WithHeadingRow
{
    protected $batch;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function __construct($batch)
    {
        $this->batch = $batch;
    }
    public function model(array $row)
    {
        $participant = Participant::find($row['id']);
        $batch_ = Batch::find($this->batch);
        if ($participant){
            $batch = new BatchParticipant();
            $batch->participant_id = $participant->id;
            $batch->batch_id = $batch_->id;
            $batch->compain_id = $batch_->compain_id;
            $batch->save();
        }
    }
}

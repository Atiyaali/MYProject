<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class BatchParticipantExport implements FromView
{
    /**
    * @return \Illuminate\Support\FromView
    */
    protected $batch;

    public function __construct($batch)
    {
        $this->batch = $batch;

        // dd($this->batch);
    }

    public function view(): View
    {
        return view('exports.batch-participant', [
            'batchs' => $this->batch

        ]);
    }
}

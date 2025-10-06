<?php

namespace App\Jobs;

use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\BatchParticipant;
class SendBatchEmailJob implements ShouldQueue
{
    use Queueable;

    protected $batch;
    protected $cc;
    protected $bcc;
    protected $email;


    /**
     * Create a new job instance.
     */
    public function __construct($batch, $cc = [], $email = null, $bcc = [])
    {
        $this->batch = $batch;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->email = $email;

        // dd($this->email);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

        try {

            $mailName = $this->batch->compain->path;

            Setting::configureSmtp();

            if (!empty($this->batch->participant->cc)) {
                $emails = explode(',', $this->batch->participant->cc); // Split the string into an array
                $this->cc = array_merge($this->cc, array_map('trim', $emails)); // Trim spaces and merge into $this->cc
            }



            if (!empty($this->batch->participant->bcc)) {
                $emails = explode(',', $this->batch->participant->bcc); // Split the string into an array
                $this->bcc = array_merge($this->bcc, array_map('trim', $emails)); // Trim spaces and merge into $this->cc
            }
// dd($this->email);

Mail::to($this->email)->cc($this->cc)->bcc($this->bcc)->send(new $mailName($this->batch));

            // Mail::to($this->batch->participant->email)->cc($this->cc)->bcc($this->bcc)->send(new $mailName($this->batch));

            $this->batch->update([
                'status' => "sent",
                'description' => 'EDM Sent to ' . $this->email,
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            $this->batch->update([
                'status' => "failed" . $e->getMessage(),
            ]);
        }
    }
}

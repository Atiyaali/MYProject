<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
use Illuminate\Support\Facades\Schema;

class AcknowledgementEdm extends Mailable
{
    use Queueable, SerializesModels;

    public $batch;
    public $subjectLine;
    public $bodyContent;
    /**
     * Create a new message instance.
     */
public function __construct($batch = null)
    {
$this->batch = $batch;
$participant  = $batch->participant;
$campaignBody = $batch->compain->body ?? '';
$this->bodyContent = $this->replacePlaceholdersAndImages($campaignBody, $participant);
    }


public function build()
{
    return $this->subject($this->subjectLine)
                ->markdown('emails.participant.edm', [
                    'bodyContent' => $this->bodyContent,
                    'batch'       => $this->batch,
                ])
                ->withSymfonyMessage(function ($message) {
                    // If you still need inline images:
                    preg_match_all('/\/storage\/campaigns\/([^"\']+)/i', $this->bodyContent, $matches);
                    foreach ($matches[1] as $filename) {
                        $path = storage_path('app/public/campaigns/' . $filename);
                        if (file_exists($path)) {
                            $message->embedFromPath($path, $filename);
                        }
                    }
                });
}




        protected function replacePlaceholdersAndImages(string $body, Participant $participant): string
    {

        $columns = Schema::getColumnListing($participant->getTable());
        foreach ($columns as $column) {
            $placeholder = '{' . $column . '}';
            $value = $participant->$column ?? '';
            $body = str_replace($placeholder, $value, $body);
        }




        return $body;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

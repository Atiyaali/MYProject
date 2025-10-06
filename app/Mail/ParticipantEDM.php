<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;
class ParticipantEDM extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $bannerCid = null;
    /**
     * Create a new message instance.
     */
    public function __construct($participant)
    {
         $this->participant = $participant;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
            subject: 'Confirmation EDM for participant',

        );

    }

    public function build(): void
    {
        // Log::info("ParticipantEDM build method called.");
        $this->markdown('emails.participant.edm')
                    ->with(['participant' => $this->participant]);

        if ($this->participant->banner_image) {
            $path = storage_path('app/public/' . $this->participant->banner_image);
            // Log::info("Banner candidate path: " . $path);
            if (file_exists($path)) {
                $this->bannerCid = 'banner_image';
                $this->attachData(file_get_contents($path), $this->bannerCid, ['mime' => 'image/jpeg', 'as' => $this->bannerCid]);
                // Log::info("Banner embedded with CID: " . $this->bannerCid);
            }

            // else {
            //     Log::error("File not found at: " . $path);
            // }
        }
        // else {
        //     Log::warning("Participant has no banner_image");
        // }
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

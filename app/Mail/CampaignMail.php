<?php

// namespace App\Mail;

// use Illuminate\Bus\Queueable;
// use Illuminate\Mail\Mailable;
// use Illuminate\Queue\SerializesModels;
// use App\Models\Participant;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Support\Facades\Schema;

// class CampaignMail extends Mailable implements ShouldQueue
// {
//     use Queueable, SerializesModels;

//     public $participant;
//     public $subjectLine;
//     public $bodyContent;

//     public function __construct(Participant $participant, string $subject, string $body)
//     {
//         $this->participant = $participant;
//         $this->subjectLine = $subject;

//         // Replace placeholders with participant data and fix image URLs
//         $this->bodyContent = $this->replacePlaceholdersAndImages($body, $participant);
//     }

//  public function build()
// {

//     $bodyContent = preg_replace_callback(
//         '/<img[^>]+src=["\']\/storage\/campaigns\/([^"\']+)["\']/i',
//         function ($matches) {
//             $filename = $matches[1];
//             return 'src="cid:' . $filename . '"';
//         },
//         $this->bodyContent
//     );

//     return $this->subject($this->subjectLine)
//         ->html($bodyContent)
//         ->withSymfonyMessage(function ($message) use ($bodyContent) {

//             preg_match_all('/cid:([^"\']+)/i', $bodyContent, $matches);

//             foreach ($matches[1] as $filename) {
//                 $path = storage_path('app/public/campaigns/' . $filename);

//                 if (file_exists($path)) {

//                     $message->embedFromPath($path, $filename);
//                 }
//             }
//         });
// }


//     protected function replacePlaceholdersAndImages(string $body, Participant $participant): string
//     {

//         $columns = Schema::getColumnListing($participant->getTable());
//         foreach ($columns as $column) {
//             $placeholder = '{' . $column . '}';
//             $value = $participant->$column ?? '';
//             $body = str_replace($placeholder, $value, $body);
//         }




//         return $body;
//     }
// }

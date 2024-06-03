<?php

// SurveyNotification.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $link;

    public function __construct($subject, $content,$link)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.survey');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class disapprovedRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details, $subject)
    {
        $this->details = $details;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->subject)
            ->from('noreply@ideaserv.com.ph')
            ->view('emails/disapprovedRequest');
            foreach($this->details['files'] as $file){
                $this->attach($file);
            }
        
        return $this;
    }
}

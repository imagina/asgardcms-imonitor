<?php

namespace Modules\Imonitor\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Alert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $alert;
    public $subject;
    public $view;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($alert, $subject, $view)
    {
        $this->alert=$alert;
        $this->subject=$subject;
        $this->view=$view;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->view)->subject($this->subject);
    }
}

<?php

namespace Modules\Imonitor\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAlertClient extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $product;
    public $subject;
    public $view;

    /**
     * Create a new message instance.
     *
     * @param $product
     * @param $subject
     * @param $view
     */
    public function __construct($product, $subject, $view)
    {
        $this->product=$product;
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

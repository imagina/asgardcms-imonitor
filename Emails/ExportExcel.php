<?php

namespace Modules\Imonitor\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportExcel extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    /**
     * @var UserInterface
     */
    public $user;

    public $path;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $path)
    {

        $this->user  = $user;

        $this->path  = $path;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('imonitor::frontend.emails.NotifyUserOfCompletedExport')
                    ->subject('MONITOR | LINK DE DESCARGA')
                    ->with([
                        'user'       =>  $this->user,
                        'path'       =>  $this->path,
                    ]);
    }
}

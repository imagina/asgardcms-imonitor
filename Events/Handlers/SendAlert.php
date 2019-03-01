<?php
/**
 * Created by PhpStorm.
 * User: imagina
 * Date: 16/01/2019
 * Time: 10:18 AM
 */

namespace Modules\Imonitor\Events\Handlers;
use Illuminate\Contracts\Mail\Mailer;
use Modules\Imonitor\Emails\Alert;
use Modules\Imonitor\Events\AlertWasCreated;

class SendAlert
{

    private $mail;
    private $setting;

    public function __construct(Mailer $mail)
    {
        $this->mail = $mail;
        $this->setting = app('Modules\Setting\Contracts\Setting');
    }

    public function handle(AlertWasCreated $event)
    {
        $alert = $event->entity;
        $product=$alert->product;
        if(!$product->mainenance){
            $subject = trans("imonitor::alerts.messages.subject")." ".$product->title." ".trans('imonitor::variables.title.variables').":".$alert->record->variable->title."-".$alert->record->created_at;
            $view = "imonitor::frontend.emails.alert";


            $this->mail->to($product->operator->email??'Info@imonotor.im')->send(new Alert($alert,$subject,$view));

            $email_to = $this->setting->get('imonitor::adminEmail')!==null? explode(',', $this->setting->get('imonitor::adminEmail')):env('MAIL_FROM_ADDRESS');

            $this->mail->to($email_to)->send(new Alert($alert,$subject,$view));
        }


    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    public function __construct($data)
    {
        $data['logo'] = 'https://digitallendingfe-dev.bankdki.co.id/eform-new/assets/images/bankdki-logo-d87f98851bad9346a293aa80fe69d250.svg';
        $data['url'] = env('CLIENT_HOST');
        $this->data = $data;
    }

    public function build()
    {
        return $this->view('emails.mail_reset_password')
                    ->subject("Atur Ulang Kata Kunci")
                    ->with($this->data);
    }
}

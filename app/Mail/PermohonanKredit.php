<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PermohonanKredit extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $data['logo'] = 'https://digitallendingfe-dev.bankdki.co.id/eform-new/assets/images/bankdki-logo-d87f98851bad9346a293aa80fe69d250.svg';
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.permohonan-kredit')
                    ->subject("Permohonan Pengajuan Kredit")
                    ->with($this->data);
    }
}

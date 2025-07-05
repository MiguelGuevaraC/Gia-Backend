<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lottery_ticket;
    public $name_aplication;
    /**
     * Crea una nueva instancia del mailable.
     *
     * @param string $lottery_ticket
     */
    public function __construct($lottery_ticket)
    {
        $this->lottery_ticket = $lottery_ticket;
        $this->name_aplication = 'Gia Lounge';
    }

    /**
     * Construye el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ticket_email') // Vista del correo
                    ->subject('Ticket para el Sorteo...') // Asunto del correo
                    ->with(['ticket' => $this->lottery_ticket,
                    'name_aplication' =>  $this->name_aplication
                ]); // Datos enviados a la vista
    }
}

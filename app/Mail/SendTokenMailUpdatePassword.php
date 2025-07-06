<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendTokenMailUpdatePassword extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $name_aplication;
    /**
     * Crea una nueva instancia del mailable.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->name_aplication = 'Gia Lounge';
    }

    /**
     * Construye el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.token_update_password') // Vista del correo
                    ->subject('Actualizar Contraseña: Tu Token de Verificación es...') // Asunto del correo
                    ->with(['token' => $this->token,
                    'name_aplication' =>  $this->name_aplication
                ]); // Datos enviados a la vista
    }
}

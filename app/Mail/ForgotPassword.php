<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $passwordreset;
    public $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $token_data)
    {
        $this->passwordreset = $data;
        $this->token = $token_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reset Password')
                    ->view('mail.forgotpassword')
                    ->with([
                        'passwordreset' => $this->passwordreset, 
                        'token' => $this->token,
                    ]);
    }
}

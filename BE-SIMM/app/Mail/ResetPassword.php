<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $recipient;
    public $role;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $recipient, $role)
    {
        $this->user = $user;
        $this->recipient = $recipient;
        $this->role = $role;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address (env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Berhasil Reset Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = [];

        if ($this->role === 'Peserta') {
            $data['nama_peserta'] = $this->recipient->nama_peserta;
            $data['nomor_induk'] = $this->recipient->nomor_induk;
        } elseif ($this->role === 'Kepala Bagian') {
            $data['nama_kabag'] = $this->recipient->nama_kabag;
            $data['nip_kabag'] = $this->recipient->nip_kabag;
        } elseif ($this->role === 'Admin') {
            $data['nama_admin'] = $this->recipient->nama_admin;
            $data['role'] = $this->role;
        }

        return new Content(
            view: 'mail.ResetPassword',
            with: $data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Attach the logo image as inline.
     */
    public function build()
    {
        return $this->view('mail.OtpMail')
            ->attach(
                public_path('template/logo.png'),
                [
                    'as' => 'logo.png',
                    'mime' => 'image/png',
                    'disposition' => 'inline',
                ]
            );
    }
}

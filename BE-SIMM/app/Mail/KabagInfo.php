<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\KepalaBagian;
use App\Models\Lokasi;
use Illuminate\Mail\Mailables\Address;

class KabagInfo extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $kabag;
    public $lokasi;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, KepalaBagian $kabag, Lokasi $lokasi)
    {
        $this->user = $user;
        $this->kabag = $kabag;
        $this->lokasi = $lokasi;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address (env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Informasi Akun Kepala Bagian',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.KabagInfo',
            with: [
                'email' => $this->user->email,
                'password' => substr($this->kabag->nip_kabag, 0, 10),
                'nama_kabag' => $this->kabag->nama_kabag,
                'nip_kabag' => $this->kabag->nip_kabag,
                'nama_lokasi' => $this->lokasi->nama_lokasi,
            ]
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
        return $this->view('mail.KabagInfo')
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

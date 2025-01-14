<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Peserta;
use Illuminate\Mail\Mailables\Address;

class AccountInfo extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $peserta;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Peserta $peserta)
    {
        $this->user = $user;
        $this->peserta = $peserta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address (env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Informasi Akun',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.AccountInfo',
            with: [
                'email' => $this->user->email,
                'nama_peserta' => $this->peserta->nama_peserta,
                'nomor_induk' => $this->peserta->nomor_induk,
                'nama_institusi' => $this->peserta->institusi->nama_institusi,
                'jurusan' => $this->peserta->jurusan,
                'nama_lokasi' => $this->peserta->lokasi->nama_lokasi,
                'tanggal_mulai' => $this->peserta->tanggal_mulai,
                'tanggal_selesai' => $this->peserta->tanggal_selesai,
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
        return $this->view('mail.AccountInfo')
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

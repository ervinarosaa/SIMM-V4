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
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use Illuminate\Mail\Mailables\Address;

class DokumenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $peserta;
    public $dokumen;
    public $jenisDokumen;

    /**
     * Create a new message instance.
     */
    public function __construct(Peserta $peserta, Dokumen $dokumen, JenisDokumen $jenisDokumen)
    {
        $this->peserta = $peserta;
        $this->dokumen = $dokumen;
        $this->jenisDokumen = $jenisDokumen;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address (env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Administrasi Magang',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.DokumenMail',
            with: [
                'nama_peserta' => $this->peserta->nama_peserta,
                'nomor_induk' => $this->peserta->nomor_induk,
                'nama_institusi' => $this->peserta->institusi->nama_institusi,
                'jurusan' => $this->peserta->jurusan,
                'nama_jenis' => $this->jenisDokumen->nama_jenis,
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
        return $this->view('mail.DokumenMail')
            ->attach(
                public_path('template/logo.png'),
                [
                    'as' => 'logo.png',
                    'mime' => 'image/png',
                    'disposition' => 'inline',
                ]
            )
            // Lampirkan dokumen peserta
            ->attach(
                storage_path("app/public/dokumen/" . basename($this->dokumen->file)),
                [
                    'as' => basename($this->dokumen->file),
                    'mime' => mime_content_type(storage_path("app/public/dokumen/" . basename($this->dokumen->file))),
                ]
            );
    }
}

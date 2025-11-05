<?php

namespace App\Mail;

use App\Models\Cours;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoursPublie extends Mailable
{
    use Queueable, SerializesModels;

    public Cours $cours;
    public ?User $destinataire;

    public function __construct(Cours $cours, ?User $destinataire = null)
    {
        $this->cours = $cours;
        $this->destinataire = $destinataire;
    }

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Publication du cours: '.$this->cours->nom_cours,
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.cours_publie',
            with: [
                'cours' => $this->cours,
                'destinataire' => $this->destinataire,
            ],
        );
    }
}

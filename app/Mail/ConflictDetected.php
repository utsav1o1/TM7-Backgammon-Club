<?php

namespace App\Mail;

use App\Models\TournamentMatch;
use App\Models\TournamentSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConflictDetected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TournamentMatch $match,
        public string $p1Name,
        public string $p2Name,
        public string $tournamentType
    ) {}

    public function envelope(): Envelope
    {
        $adminEmail = TournamentSetting::get('admin_cc_email', config('mail.from.address'));

        return new Envelope(
            to: $adminEmail,
            subject: '⚠️ TM7 Conflict Alert: ' . ucfirst($this->tournamentType) . ' Tournament — Both Players Claimed a Win',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.conflict-detected',
        );
    }
}

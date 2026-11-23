<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class RegistrationStatusMail extends Mailable
{
    public function __construct(
        public string $recipientName,
        public string $category,
        public string $status,
        public string $brandName,
        public string $contactEmail,
    ) {}

    public function build(): self
    {
        $subject = match ($this->status) {
            'received' => 'Pendaftaran diterima',
            'approved' => 'Pendaftaran disetujui',
            'rejected' => 'Pendaftaran belum disetujui',
        };

        return $this->subject($subject . ' - ' . $this->brandName)
            ->view('emails.registration-status');
    }
}

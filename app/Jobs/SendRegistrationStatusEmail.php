<?php

namespace App\Jobs;

use App\Mail\RegistrationStatusMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRegistrationStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [60, 300, 900, 1800];

    public function __construct(
        public string $email,
        public string $name,
        public string $category,
        public string $status,
        public string $brandName,
        public string $contactEmail,
    ) {
        $this->onConnection('database');
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new RegistrationStatusMail(
            $this->name,
            $this->category,
            $this->status,
            $this->brandName,
            $this->contactEmail,
        ));
    }
}

<?php

namespace App\Services;

use App\Jobs\SendRegistrationStatusEmail;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use LogicException;

class RegistrationMailQueue
{
    public static function ensureTransactional(): void
    {
        $connection = config('queue.connections.database.connection');
        if ($connection !== null && $connection !== DB::getDefaultConnection()) {
            throw new LogicException('Antrean email harus menggunakan koneksi database aplikasi.');
        }
    }

    public static function send(string $email, string $name, string $category, string $status): void
    {
        self::ensureTransactional();

        Bus::dispatch(new SendRegistrationStatusEmail(
            $email,
            $name,
            $category,
            $status,
            config('brand.current.name'),
            config('brand.current.contact_email'),
        ));
    }
}

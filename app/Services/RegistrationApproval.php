<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegistrationApproval
{
    public static function clients(array $ids, int $role, bool $approved): array
    {
        return DB::transaction(function () use ($ids, $role, $approved) {
            $missing = [];
            $status = $approved ? ($role === 2 ? 'P' : 'Y') : 'R';

            foreach (array_unique($ids) as $id) {
                $client = DB::table('tclient')->where('client_id', $id)->lockForUpdate()->first();
                if (! $client || $client->flag_appr === $status) {
                    continue;
                }

                DB::table('tclient')->where('client_id', $id)->update([
                    'flag_appr' => $status,
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ]);

                if ($status !== 'P' && ! self::notify($client->email, $client->instansi_name ?: $client->application_name, 'client', $approved ? 'approved' : 'rejected', $id)) {
                    $missing[] = $id;
                }
            }

            return $missing;
        });
    }

    public static function partners(array $ids, string $category, bool $approved): array
    {
        return DB::transaction(function () use ($ids, $category, $approved) {
            $missing = [];
            $status = $approved ? 1 : 2;
            $type = $category === 'supplier' ? '1' : '2';

            foreach (array_unique($ids) as $id) {
                $partner = DB::table('tcompany')->where('id', $id)->where('type', $type)->lockForUpdate()->first();
                if (! $partner) {
                    continue;
                }

                $updated = DB::table('users')->where('client_id', $id)->where('status', '!=', $status)->update([
                    'status' => $status,
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ]);

                if ($updated && ! self::notify($partner->email, $partner->name, $category, $approved ? 'approved' : 'rejected', $id)) {
                    $missing[] = $id;
                }
            }

            return $missing;
        });
    }

    private static function notify(?string $email, ?string $name, string $category, string $status, string $id): bool
    {
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Notifikasi pendaftaran tidak dikirim karena email tidak tersedia atau tidak valid.', [
                'category' => $category,
                'id' => $id,
                'status' => $status,
            ]);

            return false;
        }

        RegistrationMailQueue::send($email, $name ?: 'Pendaftar', $category, $status);

        return true;
    }

    public static function response(array $missing): array
    {
        return [
            'message' => 'Status pendaftaran berhasil diperbarui.',
            'warning' => $missing
                ? 'Status pendaftaran berhasil diperbarui, tetapi email tidak dikirim karena alamat email tidak tersedia atau tidak valid untuk ID: ' . implode(', ', $missing)
                : null,
        ];
    }
}

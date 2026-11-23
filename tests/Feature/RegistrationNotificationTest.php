<?php

namespace Tests\Feature;

use App\Jobs\SendRegistrationStatusEmail;
use App\Mail\RegistrationStatusMail;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'queue.connections.database.connection' => null,
        ]);
        DB::purge('sqlite');

        foreach ([
            '0001_01_01_000000_create_users_table.php',
            '0001_01_01_000002_create_jobs_table.php',
            '2024_11_01_104219_create_tclient_table.php',
            '2024_11_30_211242_alter_flag_appr_tclient_table.php',
            '2024_12_18_183727_alter_rename_negara_id_tclient_table.php',
            '2026_11_24_000001_add_email_to_tclient_table.php',
        ] as $migration) {
            (require database_path('migrations/' . $migration))->up();
        }
        Schema::table('users', function (Blueprint $table) {
            $table->string('client_id')->nullable();
            $table->integer('role')->default(1);
            $table->integer('status')->default(0);
        });
        Schema::create('tcompany', function (Blueprint $table) {
            $table->string('id')->primary();
            foreach (['name', 'email', 'country', 'province', 'regency', 'district', 'subdistrict', 'postal_code', 'address', 'telephone', 'handphone', 'director', 'position', 'handphone_director', 'pic', 'handphone_pic', 'file', 'type', 'noref', 'agreement', 'created_by', 'updated_by'] as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
        });
        Schema::create('tcompany_bank_account', function (Blueprint $table) {
            foreach (['client_id', 'npwp', 'account_bank', 'bank', 'account_name', 'bank_city', 'created_by', 'updated_by'] as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
        });
    }

    public function test_client_registration_stores_email_and_queues_receipt(): void
    {
        Storage::fake('public');
        DB::table('tcompany')->insert(['id' => 'distributor-1', 'noref' => 'REF123']);

        $this->post('/pendaftaran-client', $this->clientData(), ['Accept' => 'application/json'])->assertCreated();

        $this->assertDatabaseHas('tclient', ['email' => 'client@example.test', 'company_id' => 'distributor-1']);
        $job = $this->statusJobs()[0];
        $this->assertSame('received', $job->status);
        $this->assertSame('client', $job->category);
        $this->assertSame('client@example.test', $job->email);

        Mail::fake();
        $this->artisan('queue:work', ['connection' => 'database', '--once' => true])->assertSuccessful();
        Mail::assertSent(RegistrationStatusMail::class, function ($mail) {
            return $mail->hasTo('client@example.test')
                && str_contains($mail->render(), 'Pendaftaran client Anda telah kami terima');
        });
    }

    private function clientData(): array
    {
        return [
            'name' => 'Sekolah',
            'email' => 'client@example.test',
            'name_apps' => 'Perpustakaan Sekolah',
            'web_add' => 'sekolah',
            'address' => 'Alamat',
            'country' => 'IDN',
            'province' => '1',
            'regency' => '1',
            'district' => '1',
            'subdistrict' => '1',
            'postal_code' => '12345',
            'npwp' => '123',
            'pers_responsible' => 'Nama',
            'pos_pers_responsible' => 'Jabatan',
            'mou_sign_name' => 'Nama',
            'pos_sign_name' => 'Jabatan',
            'administrator_name' => 'Admin',
            'administrator_phone' => '08123456789',
            'logo_big' => UploadedFile::fake()->image('logo.png'),
            'logo_small' => UploadedFile::fake()->image('icon.png'),
            'agreement' => 'true',
            'noref' => 'REF123',
        ];
    }

    public function test_partner_registration_queues_receipt_and_email_verification(): void
    {
        foreach (['supplier', 'distributor'] as $category) {
            $email = $category . '@example.test';
            $this->postJson('/pendaftaran-' . $category, $this->partnerData($category))->assertCreated();

            $this->assertDatabaseHas('users', ['email' => $email]);
            $jobs = $this->statusJobs();
            $job = end($jobs);
            $this->assertSame($category, $job->category);
            $this->assertSame($email, $job->email);
            auth()->logout();
        }

        $this->assertSame(4, DB::table('jobs')->count());
        $queued = DB::table('jobs')->get()->map(fn ($row) => unserialize(json_decode($row->payload)->data->command));
        $this->assertCount(2, $queued->filter(fn ($job) => $job instanceof SendQueuedNotifications));

        config(['mail.default' => 'array']);
        Mail::forgetMailers();
        for ($i = 0; $i < 4; $i++) {
            $this->artisan('queue:work', ['connection' => 'database', '--once' => true])->assertSuccessful();
        }
        $this->assertSame(0, DB::table('jobs')->count());
        $this->assertCount(4, Mail::mailer()->getSymfonyTransport()->messages());
    }

    public function test_final_decisions_queue_notices_and_legacy_client_warns(): void
    {
        DB::table('tclient')->insert([
            ['client_id' => 'c1', 'instansi_name' => 'Sekolah', 'email' => 'client@example.test', 'flag_appr' => 'N'],
            ['client_id' => 'old', 'instansi_name' => 'Lama', 'email' => null, 'flag_appr' => 'N'],
            ['client_id' => 'c2', 'instansi_name' => 'Sekolah 2', 'email' => 'client2@example.test', 'flag_appr' => 'N'],
        ]);
        DB::table('tcompany')->insert([
            ['id' => 's1', 'type' => '1', 'name' => 'Supplier', 'email' => 'supplier@example.test'],
            ['id' => 'd1', 'type' => '2', 'name' => 'Distributor', 'email' => 'distributor@example.test'],
            ['id' => 's2', 'type' => '1', 'name' => 'Supplier 2', 'email' => 'supplier2@example.test'],
            ['id' => 'd2', 'type' => '2', 'name' => 'Distributor 2', 'email' => 'distributor2@example.test'],
        ]);
        DB::table('users')->insert([
            ['name' => 'Supplier', 'email' => 'supplier@example.test', 'password' => 'x', 'client_id' => 's1', 'status' => 0],
            ['name' => 'Distributor', 'email' => 'distributor@example.test', 'password' => 'x', 'client_id' => 'd1', 'status' => 0],
            ['name' => 'Supplier 2', 'email' => 'supplier2@example.test', 'password' => 'x', 'client_id' => 's2', 'status' => 0],
            ['name' => 'Distributor 2', 'email' => 'distributor2@example.test', 'password' => 'x', 'client_id' => 'd2', 'status' => 0],
        ]);

        $this->withoutMiddleware()->actingAs(new User(['role' => 2]));
        $this->postJson('/transactions/approval-client', ['c1'])->assertCreated();
        $this->assertDatabaseHas('tclient', ['client_id' => 'c1', 'flag_appr' => 'P']);
        $this->assertCount(0, $this->statusJobs());

        $this->actingAs(new User(['role' => 0]));
        $this->postJson('/transactions/approval-client', ['c1', 'old'])->assertCreated()->assertJsonPath('warning', 'Status pendaftaran berhasil diperbarui, tetapi email tidak dikirim karena alamat email tidak tersedia atau tidak valid untuk ID: old');
        $this->postJson('/transactions/approval-supplier', ['s1'])->assertCreated();
        $this->postJson('/transactions/approval-distributor/reject', ['d1'])->assertCreated();
        $this->postJson('/transactions/approval-client/reject', ['c2'])->assertCreated();
        $this->postJson('/transactions/approval-supplier/reject', ['s2'])->assertCreated();
        $this->postJson('/transactions/approval-distributor', ['d2'])->assertCreated();
        $jobs = $this->statusJobs();
        $this->assertSame(['approved', 'approved', 'rejected', 'rejected', 'rejected', 'approved'], array_map(fn ($job) => $job->status, $jobs));
        $this->assertSame(['client', 'supplier', 'distributor', 'client', 'supplier', 'distributor'], array_map(fn ($job) => $job->category, $jobs));
        $this->assertDatabaseHas('tclient', ['client_id' => 'old', 'flag_appr' => 'Y']);

        $this->postJson('/transactions/approval-supplier', ['s1'])->assertCreated();
        $this->assertCount(6, $this->statusJobs());
    }

    public function test_queue_failure_rolls_back_approval(): void
    {
        DB::table('tclient')->insert(['client_id' => 'c1', 'instansi_name' => 'Sekolah', 'email' => 'client@example.test', 'flag_appr' => 'N']);
        Schema::drop('jobs');

        $this->withoutMiddleware()->actingAs(new User(['role' => 0]));
        $this->postJson('/transactions/approval-client', ['c1'])->assertStatus(500);
        $this->assertDatabaseHas('tclient', ['client_id' => 'c1', 'flag_appr' => 'N']);
    }

    public function test_queue_failure_rolls_back_partner_registration(): void
    {
        Schema::drop('jobs');
        $this->postJson('/pendaftaran-supplier', $this->partnerData('supplier'))->assertStatus(500);
        $this->assertDatabaseMissing('users', ['email' => 'supplier@example.test']);
        $this->assertDatabaseMissing('tcompany', ['email' => 'supplier@example.test']);
    }

    public function test_queue_failure_rolls_back_client_registration_and_cleans_uploaded_logos(): void
    {
        Storage::fake('public');
        DB::table('tcompany')->insert(['id' => 'distributor-1', 'noref' => 'REF123']);
        Schema::drop('jobs');

        $this->post('/pendaftaran-client', $this->clientData(), ['Accept' => 'application/json'])->assertStatus(500);
        $this->assertDatabaseMissing('tclient', ['email' => 'client@example.test']);
        $this->assertSame([], Storage::disk('public')->allFiles('images/logo'));
    }

    public function test_failed_email_is_released_for_retry(): void
    {
        dispatch(new SendRegistrationStatusEmail('client@example.test', 'Sekolah', 'client', 'received', 'Brand', 'info@example.test'));
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('SMTP unavailable'));

        $this->artisan('queue:work', ['connection' => 'database', '--once' => true]);
        $job = DB::table('jobs')->first();
        $this->assertNotNull($job);
        $this->assertSame(1, $job->attempts);
        $this->assertGreaterThan(time(), $job->available_at);
    }

    private function partnerData(string $category): array
    {
        return [
            'name' => ucfirst($category), 'email' => $category . '@example.test', 'password' => 'password123',
            'password_confirmation' => 'password123', 'country' => 'IDN', 'province' => '1',
            'regency' => '1', 'district' => '1', 'subdistrict' => '1', 'postal_code' => '12345',
            'address' => 'Alamat', 'telephone' => '', 'handphone' => '08123', 'director' => 'Nama',
            'position' => 'Direktur', 'handphone_director' => '08123', 'person_in_charge' => 'Nama',
            'handphone_person_in_charge' => '08123', 'imprint' => [], 'publisher' => [], 'category' => [],
            'npwp' => '123', 'account_bank' => '123', 'bank' => 'Bank', 'account_name' => 'Nama',
            'bank_city' => 'Kota', 'supplier' => $category === 'supplier',
            'distributor' => $category === 'distributor', 'supp_distributor' => false,
            'agreement' => true, 'noref' => 'REF123',
        ];
    }

    private function statusJobs(): array
    {
        return DB::table('jobs')->get()->map(function ($row) {
            return unserialize(json_decode($row->payload)->data->command);
        })->filter(fn ($job) => $job instanceof SendRegistrationStatusEmail)->values()->all();
    }
}

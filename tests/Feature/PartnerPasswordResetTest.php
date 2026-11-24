<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PartnerPasswordResetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'queue.connections.database.connection' => null,
            'mail.default' => 'array',
        ]);
        DB::purge('sqlite');
        Mail::forgetMailers();

        (require database_path('migrations/0001_01_01_000000_create_users_table.php'))->up();
        (require database_path('migrations/0001_01_01_000002_create_jobs_table.php'))->up();
        Schema::table('users', function (Blueprint $table) {
            $table->string('client_id')->nullable();
            $table->integer('role')->default(1);
            $table->integer('status')->default(1);
        });
        Schema::create('tcompany', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('type');
        });
        DB::table('tcompany')->insert([
            ['id' => 'supplier', 'type' => '1'],
            ['id' => 'distributor', 'type' => '2'],
        ]);
        foreach (['supplier' => 1, 'distributor' => 2] as $id => $role) {
            User::create(['name' => $id, 'email' => $id . '@example.test', 'password' => 'old-password', 'client_id' => $id, 'role' => $role]);
        }
        User::create(['name' => 'admin', 'email' => 'admin@example.test', 'password' => 'old-password', 'client_id' => 'supplier', 'role' => 0]);
        User::create(['name' => 'client', 'email' => 'client@example.test', 'password' => 'old-password', 'client_id' => 'client', 'role' => 1]);
    }

    public function test_partner_receives_encrypted_queued_reset_and_can_use_it_only_once(): void
    {
        foreach (['supplier', 'distributor'] as $partner) {
            $email = $partner . '@example.test';
            $this->from('/password/reset')->post('/password/email', ['email' => $email])
                ->assertRedirect('/password/reset')->assertSessionHas('status');

            $payload = json_decode(DB::table('jobs')->latest('id')->value('payload'));
            $job = unserialize(Crypt::decrypt($payload->data->command));
            $token = $job->notification->token;
            $this->assertStringNotContainsString($token, json_encode($payload));
            $this->artisan('queue:work', ['connection' => 'database', '--once' => true])->assertSuccessful();
            $messages = Mail::mailer()->getSymfonyTransport()->messages();
            $message = $messages->last()->getOriginalMessage();
            $this->assertStringContainsString($token, $message->getHtmlBody());
            $this->assertStringContainsString(config('brand.current.name'), $message->getSubject());

            $data = ['email' => $email, 'token' => $token, 'password' => 'new-password', 'password_confirmation' => 'new-password'];
            $this->post('/password/reset', $data)->assertRedirect('/login')->assertSessionHas('status');
            $this->assertGuest();
            $this->assertTrue(Hash::check('new-password', User::where('email', $email)->firstOrFail()->password));
            $this->from('/password/reset/' . $token)->post('/password/reset', $data)->assertSessionHasErrors('email');
        }

        $this->assertCount(2, Mail::mailer()->getSymfonyTransport()->messages());
    }

    public function test_admin_client_and_unknown_accounts_do_not_get_reset_emails(): void
    {
        foreach (['admin@example.test', 'client@example.test', 'unknown@example.test'] as $email) {
            $this->from('/password/reset')->post('/password/email', ['email' => $email])
                ->assertRedirect('/password/reset')->assertSessionHas('status');
        }
        $this->assertSame(0, DB::table('jobs')->count());
        $this->assertSame(0, DB::table('password_reset_tokens')->count());
    }

    public function test_reset_pages_render_for_guests(): void
    {
        $this->withoutVite();
        $this->get('/password/reset')->assertOk()->assertSee('supplier atau distributor');
        $this->get('/password/reset/token?email=supplier@example.test')->assertOk()->assertSee('Atur ulang password');
    }

    public function test_reset_rejects_expired_tokens_and_non_partner_accounts(): void
    {
        $user = User::where('email', 'supplier@example.test')->firstOrFail();
        $token = Password::broker()->createToken($user);
        DB::table('password_reset_tokens')->where('email', $user->email)->update(['created_at' => now()->subMinutes(61)]);
        $this->from('/password/reset/' . $token)->post('/password/reset', [
            'email' => $user->email, 'token' => $token, 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));

        $admin = User::where('email', 'admin@example.test')->firstOrFail();
        $adminToken = Password::broker()->createToken($admin);
        $this->from('/password/reset/' . $adminToken)->post('/password/reset', [
            'email' => $admin->email, 'token' => $adminToken, 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('old-password', $admin->fresh()->password));
    }

    public function test_queue_failure_rolls_back_reset_token(): void
    {
        Schema::drop('jobs');
        $this->post('/password/email', ['email' => 'supplier@example.test'])->assertStatus(500);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'supplier@example.test']);
    }
}

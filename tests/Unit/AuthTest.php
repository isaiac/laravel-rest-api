<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test register.
     *
     * @return void
     */
    public function test_register()
    {
        $data = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'changeme.123',
            'password_confirmation' => 'changeme.123'
        ];

        $response = $this->post('/auth/register', $data)
            ->assertCreated()
            ->json();

        $this->assertEquals($data['username'], $response['username']);
    }

    /**
     * Test send verification email.
     *
     * @return void
     */
    public function test_send_verification_email()
    {
        $data = [
            'email' => User::where('username', 'unverified-user')->value('email')
        ];

        $response = $this->post('/auth/verification', $data);

        $response->assertNoContent();
    }

    /**
     * Test verify user.
     *
     * @return void
     */
    public function test_verify_user()
    {
        $token = AuthService::getJWTFromUser(
            User::where('username', 'unverified-user')->first()
        );

        $response = $this->get("/auth/verify?token=$token");

        $response->assertNoContent();
    }

    /**
     * Test send reset password email.
     *
     * @return void
     */
    public function test_send_reset_password_email()
    {
        $data = [
            'email' => User::where('username', 'user')->value('email')
        ];

        $response = $this->post('/auth/password', $data);

        $response->assertNoContent();
    }

    /**
     * Test update password.
     *
     * @return void
     */
    public function test_update_password()
    {
        $user = User::where('username', 'user')->first();

        $token = AuthService::getJWTFromUser(
            $user,
            ['token' => AuthService::getPasswordBroker()->getRepository()->create($user)]
        );

        $data = [
            'token' => $token,
            'password' => 'changeme.123',
            'password_confirmation' => 'changeme.123'
        ];

        $response = $this->patch('/auth/password', $data);

        $response->assertNoContent();
    }

    /**
     * Test login.
     *
     * @return void
     */
    public function test_login()
    {
        $data = [
            'login' => 'loggable-user',
            'password' => 'changeme.123'
        ];

        $response = $this->post('/auth/login', $data)
            ->assertOk()
            ->json();

        $this->assertEquals('bearer', $response['token_type']);
    }

    /**
     * Test login as.
     *
     * @return void
     */
    public function test_login_as()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $user_id = User::where('username', 'user-admin')->value('id');

        $response = $this->post("/auth/login/$user_id")
            ->assertOk()
            ->json();

        $this->assertEquals('bearer', $response['token_type']);
    }

    /**
     * Test logout.
     *
     * @return void
     */
    public function test_logout()
    {
        Sanctum::actingAs(User::where('username', 'user')->first());

        $response = $this->post('/auth/logout');

        $response->assertNoContent();
    }
}

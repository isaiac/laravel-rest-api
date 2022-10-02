<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test case.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $user = User::where('username', 'user')->first();

        Sanctum::actingAs($user, $user->getAbilities());
    }

    /**
     * Test fetch me.
     *
     * @return void
     */
    public function test_fetch_me()
    {
        $me = User::where('username', 'user')->first();

        $response = $this->get('/me')
            ->assertOk()
            ->json();

        $this->assertEquals($me->username, $response['username']);
    }

    /**
     * Test update me.
     *
     * @return void
     */
    public function test_update_me()
    {
        $data = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'changeme.123',
            'password_confirmation' => 'changeme.123'
        ];

        $response = $this->put('/me', $data)
            ->assertOk()
            ->json();

        $this->assertEquals($data['username'], $response['username']);
    }

    /**
     * Test delete me.
     *
     * @return void
     */
    public function test_delete_me()
    {
        $response = $this->delete('/me');

        $response->assertNoContent();
    }
}

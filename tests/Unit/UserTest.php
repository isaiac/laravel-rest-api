<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
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

        $admin = User::where('username', 'user-admin')->first();

        Sanctum::actingAs($admin, $admin->getAbilities());
    }

    /**
     * Test fetch users.
     *
     * @return void
     */
    public function test_fetch_users()
    {
        $response = $this->get('/users')
            ->assertOk()
            ->json();

        $this->assertEquals(User::count(), count($response));
    }

    /**
     * Test create user.
     *
     * @return void
     */
    public function test_create_user()
    {
        $data = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'changeme.123',
            'password_confirmation' => 'changeme.123',
            'status' => 'active',
            'roles' => [
                ['id' => 'admin']
            ],
            'permissions' => [
                ['id' => 'add-users'],
                ['id' => 'edit-users'],
                ['id' => 'delete-users']
            ]
        ];

        $response = $this->post('/users', $data)
            ->assertCreated()
            ->json();

        $this->assertEquals($data['username'], $response['username']);
    }

    /**
     * Test fetch user.
     *
     * @return void
     */
    public function test_fetch_user()
    {
        $user = User::where('username', 'superadmin')->first();

        $response = $this->get("/users/$user->id")
            ->assertOk()
            ->json();

        $this->assertEquals($user->username, $response['username']);
    }

    /**
     * Test update user.
     *
     * @return void
     */
    public function test_update_user()
    {
        $user_id = User::where('username', 'superadmin')->value('id');

        $data = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'username' => 'test',
            'password' => 'changeme.123',
            'password_confirmation' => 'changeme.123',
            'status' => 'active',
            'roles' => [
                ['id' => 'user']
            ],
            'permissions' => []
        ];

        $response = $this->put("/users/$user_id", $data)
            ->assertOk()
            ->json();

        $this->assertEquals($data['username'], $response['username']);
    }

    /**
     * Test delete user.
     *
     * @return void
     */
    public function test_delete_user()
    {
        $user_id = User::where('username', 'superadmin')->value('id');

        $response = $this->delete("/users/$user_id");

        $response->assertNoContent();
    }

    /**
     * Test create users batch.
     *
     * @return void
     */
    public function test_create_users_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                [
                    'name' => 'Test 1',
                    'email' => 'test1@example.com',
                    'username' => 'test1',
                    'password' => 'changeme.123',
                    'password_confirmation' => 'changeme.123',
                    'status' => 'active',
                    'roles' => [
                        ['id' => 'admin']
                    ],
                    'permissions' => [
                        ['id' => 'add-users'],
                        ['id' => 'edit-users'],
                        ['id' => 'delete-users']
                    ]
                ],
                [
                    'name' => 'Test 2',
                    'email' => 'test2@example.com',
                    'username' => 'test2',
                    'password' => 'changeme.123',
                    'password_confirmation' => 'changeme.123',
                    'status' => 'active',
                    'roles' => [
                        ['id' => 'user']
                    ]
                ]
            ]
        ];

        $response = $this->post('/users/batch', $data)
            ->assertCreated()
            ->json();

        $this->assertEquals(count($data['data']), count($response));
    }

    /**
     * Test update users batch.
     *
     * @return void
     */
    public function test_udpdate_users_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                [
                    'id' => User::where('username', 'user-admin')->value('id'),
                    'name' => 'Test 1',
                    'email' => 'test1@example.com',
                    'username' => 'test1',
                    'password' => 'changeme.123',
                    'password_confirmation' => 'changeme.123',
                    'status' => 'active',
                    'roles' => [
                        ['id' => 'admin']
                    ],
                    'permissions' => [
                        ['id' => 'add-users'],
                        ['id' => 'edit-users'],
                        ['id' => 'delete-users']
                    ]
                ],
                [
                    'id' => User::where('username', 'user')->value('id'),
                    'name' => 'Test 2',
                    'email' => 'test2@example.com',
                    'username' => 'test2',
                    'password' => 'changeme.123',
                    'password_confirmation' => 'changeme.123',
                    'status' => 'active',
                    'roles' => [
                        ['id' => 'user']
                    ]
                ]
            ]
        ];

        $response = $this->put('/users/batch', $data)
            ->assertOk()
            ->json();

        $this->assertEquals(count($data['data']), count($response));
    }

    /**
     * Test delete users batch.
     *
     * @return void
     */
    public function test_delete_users_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                ['id' => User::where('username', 'user-admin')->value('id')],
                ['id' => User::where('username', 'user')->value('id')]
            ]
        ];

        $response = $this->delete('/users/batch', $data);

        $response->assertNoContent();
    }

    /**
     * Test update users query.
     *
     * @return void
     */
    public function test_udpdate_users_query()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'status' => 'inactive'
        ];

        $response = $this->put('/users/query', $data);

        $response->assertNoContent();
    }

    /**
     * Test delete users query.
     *
     * @return void
     */
    public function test_delete_users_query()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $response = $this->delete('/users/query');

        $response->assertNoContent();
    }
}

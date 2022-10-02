<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleTest extends TestCase
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

        $admin = User::where('username', 'role-admin')->first();

        Sanctum::actingAs($admin, $admin->getAbilities());
    }

    /**
     * Test fetch roles.
     *
     * @return void
     */
    public function test_fetch_roles()
    {
        $response = $this->get('/roles')
            ->assertOk()
            ->json();

        $this->assertEquals(Role::count(), count($response));
    }

    /**
     * Test create role.
     *
     * @return void
     */
    public function test_create_role()
    {
        $data = [
            'name' => 'Test',
            'permissions' => [
                ['id' => 'add-users'],
                ['id' => 'edit-users'],
                ['id' => 'delete-users']
            ]
        ];

        $response = $this->post('/roles', $data)
            ->assertCreated()
            ->json();

        $this->assertEquals($data['name'], $response['name']);
    }

    /**
     * Test fetch role.
     *
     * @return void
     */
    public function test_fetch_role()
    {
        $role = Role::find('super-admin');

        $response = $this->get("/roles/$role->id")
            ->assertOk()
            ->json();

        $this->assertEquals($role->name, $response['name']);
    }

    /**
     * Test update role.
     *
     * @return void
     */
    public function test_update_role()
    {
        $role_id = Role::find('super-admin')->id;

        $data = [
            'name' => 'Test',
            'permissions' => []
        ];

        $response = $this->put("/roles/$role_id", $data)
            ->assertOk()
            ->json();

        $this->assertEquals($data['name'], $response['name']);
    }

    /**
     * Test delete role.
     *
     * @return void
     */
    public function test_delete_role()
    {
        $role_id = Role::find('super-admin')->id;

        $response = $this->delete("/roles/$role_id");

        $response->assertNoContent();
    }

    /**
     * Test create roles batch.
     *
     * @return void
     */
    public function test_create_roles_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                [
                    'name' => 'Test 1',
                    'permissions' => [
                        ['id' => 'add-users'],
                        ['id' => 'edit-users'],
                        ['id' => 'delete-users']
                    ]
                ],
                [
                    'name' => 'Test 2'
                ]
            ]
        ];

        $response = $this->post('/roles/batch', $data)
            ->assertCreated()
            ->json();

        $this->assertEquals(count($data['data']), count($response));
    }

    /**
     * Test update roles batch.
     *
     * @return void
     */
    public function test_udpdate_roles_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                [
                    'id' => Role::find('admin')->id,
                    'name' => 'Test 1',
                    'permissions' => [
                        ['id' => 'add-users'],
                        ['id' => 'edit-users'],
                        ['id' => 'delete-users']
                    ]
                ],
                [
                    'id' => Role::find('user')->id,
                    'name' => 'Test 2'
                ]
            ]
        ];

        $response = $this->put('/roles/batch', $data)
            ->assertOk()
            ->json();

        $this->assertEquals(count($data['data']), count($response));
    }

    /**
     * Test delete roles batch.
     *
     * @return void
     */
    public function test_delete_roles_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                ['id' => Role::find('admin')->id],
                ['id' => Role::find('user')->id]
            ]
        ];

        $response = $this->delete('/roles/batch', $data);

        $response->assertNoContent();
    }

    /**
     * Test update roles query.
     *
     * @return void
     */
    public function test_udpdate_roles_query()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'created_at' => null
        ];

        $response = $this->put('/roles/query', $data);

        $response->assertNoContent();
    }

    /**
     * Test delete roles query.
     *
     * @return void
     */
    public function test_delete_roles_query()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $response = $this->delete('/roles/query');

        $response->assertNoContent();
    }
}

<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PermissionTest extends TestCase
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

        $admin = User::where('username', 'permission-admin')->first();

        Sanctum::actingAs($admin, $admin->getAbilities());
    }

    /**
     * Test fetch permissions.
     *
     * @return void
     */
    public function test_fetch_permissions()
    {
        $response = $this->get('/permissions')
            ->assertOk()
            ->json();

        $this->assertEquals(Permission::count(), count($response));
    }

    /**
     * Test create permission.
     *
     * @return void
     */
    public function test_create_permission()
    {
        $data = [
            'name' => 'Test'
        ];

        $response = $this->post('/permissions', $data)
            ->assertCreated()
            ->json();

        $this->assertEquals($data['name'], $response['name']);
    }

    /**
     * Test fetch permission.
     *
     * @return void
     */
    public function test_fetch_permission()
    {
        $permission = Permission::find('add-users');

        $response = $this->get("/permissions/$permission->id")
            ->assertOk()
            ->json();

        $this->assertEquals($permission->name, $response['name']);
    }

    /**
     * Test update permission.
     *
     * @return void
     */
    public function test_update_permission()
    {
        $permission_id = Permission::find('add-users')->id;

        $data = [
            'name' => 'Test'
        ];

        $response = $this->put("/permissions/$permission_id", $data)
            ->assertOk()
            ->json();

        $this->assertEquals($data['name'], $response['name']);
    }

    /**
     * Test delete permission.
     *
     * @return void
     */
    public function test_delete_permission()
    {
        $permission_id = Permission::find('add-users')->id;

        $response = $this->delete("/permissions/$permission_id");

        $response->assertNoContent();
    }

    /**
     * Test create permissions batch.
     *
     * @return void
     */
    public function test_create_permissions_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                ['name' => 'Test 1'],
                ['name' => 'Test 2']
            ]
        ];

        $response = $this->post('/permissions/batch', $data)
            ->assertCreated()
            ->json();

        $this->assertEquals(count($data['data']), count($response));
    }

    /**
     * Test update permissions batch.
     *
     * @return void
     */
    public function test_udpdate_permissions_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                [
                    'id' => Permission::find('add-users')->id,
                    'name' => 'Test 1'
                ],
                [
                    'id' => Permission::find('edit-users')->id,
                    'name' => 'Test 2'
                ]
            ]
        ];

        $response = $this->put('/permissions/batch', $data)
            ->assertOk()
            ->json();

        $this->assertEquals(count($data['data']), count($response));
    }

    /**
     * Test delete permissions batch.
     *
     * @return void
     */
    public function test_delete_permissions_batch()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'data' => [
                ['id' => Permission::find('add-users')->id],
                ['id' => Permission::find('edit-users')->id]
            ]
        ];

        $response = $this->delete('/permissions/batch', $data);

        $response->assertNoContent();
    }

    /**
     * Test update permissions query.
     *
     * @return void
     */
    public function test_udpdate_permissions_query()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $data = [
            'created_at' => null
        ];

        $response = $this->put('/permissions/query', $data);

        $response->assertNoContent();
    }

    /**
     * Test delete permissions query.
     *
     * @return void
     */
    public function test_delete_permissions_query()
    {
        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());

        $response = $this->delete('/permissions/query');

        $response->assertNoContent();
    }
}

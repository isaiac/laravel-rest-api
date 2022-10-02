<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Activitylog\Models\Activity as Log;
use Tests\TestCase;

class LogTest extends TestCase
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

        $superadmin = User::where('username', 'superadmin')->first();

        Sanctum::actingAs($superadmin, $superadmin->getAbilities());
    }

    /**
     * Test fetch logs.
     *
     * @return void
     */
    public function test_fetch_logs()
    {
        $response = $this->get('/logs')
            ->assertOk()
            ->json();

        $this->assertEquals(Log::count(), count($response));
    }

    /**
     * Test fetch log.
     *
     * @return void
     */
    public function test_fetch_log()
    {
        $log = Log::find(1);

        $response = $this->get("/logs/$log->id")
            ->assertOk()
            ->json();

        $this->assertEquals($log->log_name, $response['log_name']);
    }

    /**
     * Test delete log.
     *
     * @return void
     */
    public function test_delete_log()
    {
        $log_id = 1;

        $response = $this->delete("/logs/$log_id");

        $response->assertNoContent();
    }

    /**
     * Test delete logs batch.
     *
     * @return void
     */
    public function test_delete_logs_batch()
    {
        $data = [
            'data' => [
                ['id' => 1],
                ['id' => 2]
            ]
        ];

        $response = $this->delete('/logs/batch', $data);

        $response->assertNoContent();
    }

    /**
     * Test delete logs query.
     *
     * @return void
     */
    public function test_delete_logs_query()
    {
        $response = $this->delete('/logs/query');

        $response->assertNoContent();
    }
}

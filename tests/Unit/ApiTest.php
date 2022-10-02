<?php

namespace Tests\Unit;

use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * Test ping.
     *
     * @return void
     */
    public function test_ping()
    {
        $response = $this->get('/ping');

        $response->assertOk();
    }
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    /**
     * Setup the test case.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->withHeaders($this->getDefaultHeaders());
    }

    /**
     * Get the default headers for all the test requests.
     *
     * @return array<string, string>
     */
    protected function getDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/'.config('api.standardsTree').'.'.config('api.subtype')
                .'.'.config('api.version').'+'.config('api.defaultFormat')
        ];
    }
}

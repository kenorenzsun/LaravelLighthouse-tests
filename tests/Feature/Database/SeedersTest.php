<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * @group database
 */
class SeedersTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_works()
    {
        $exit_code = Artisan::call('db:seed');

        $this->assertEquals(0, $exit_code, 'Seeders did not complete successfully');
    }
}

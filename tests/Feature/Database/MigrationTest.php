<?php

namespace Tests\Feature\Database;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * @group database
 */
class MigrationTest extends TestCase
{
    public function test_migrations_works()
    {
        $exit_code = Artisan::call('migrate');

        $this->assertEquals(0, $exit_code, 'Migration did not complete successfully');
    }

    public function test_fresh_works()
    {
        // pre-migrate
        Artisan::call('migrate');

        $exit_code = Artisan::call('migrate:fresh');

        $this->assertEquals(0, $exit_code, 'Migration did not complete successfully');
    }
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;

abstract class ApiResourceTestCase extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;
    use RefreshesSchemaCache;

    abstract protected function test_index_works();

    abstract protected function test_show_works();

    abstract protected function test_store_works();

    abstract protected function test_update_works();

    abstract protected function test_destroy_works();
}

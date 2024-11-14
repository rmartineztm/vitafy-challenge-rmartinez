<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\AssertableJsonString;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * Returns an AssertableJsonString from an instantiated Resource.
     * @param $resource
     * @return AssertableJsonString
     */
    protected function assertableResource($resource): AssertableJsonString
    {
        return new AssertableJsonString($resource->response()->getData(true));
    }
}

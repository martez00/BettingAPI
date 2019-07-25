<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
/**
 * Class TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    /**
     * @return TestCase
     */
    public function startJsonRequest()
    {
        return $this->withHeaders([
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }
}
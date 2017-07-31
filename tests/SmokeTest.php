<?php

use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase
{
    public function testSmoke()
    {
        $this->assertEquals(true, true, "Smoke test");
    }
}
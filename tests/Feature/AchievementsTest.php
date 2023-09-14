<?php

namespace Tests\Feature;

use Tests\TestCase;

class AchievementsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

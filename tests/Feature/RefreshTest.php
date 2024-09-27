<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    #[Test]
    public function rebuild(): void
    {
        $this->markTestIncomplete();
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->withoutMiddleware();

        $response = $this->post('/rebuild');
        $response->assertSuccessful();
    }
}

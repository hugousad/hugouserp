<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        // Assert guest status
        $this->assertGuest();

        // Expect redirect to login for unauthenticated users
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
}

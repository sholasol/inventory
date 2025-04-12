<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_login_with_valid_credential(): void
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_user_cannot_login_with_invalid_credential()
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'wrong_password'
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }


}

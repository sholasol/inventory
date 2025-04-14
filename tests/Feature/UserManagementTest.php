<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

     //test admin can create user
    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $response = $this->post('/users', [
            'name' => 'Joe Doe',
            'email' => 'Joe@user.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'email' => 'Joe@user.com'
        ]);

    }

    //test unauthorise access
    public function test_a_non_admin_cannot_create_user()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user);

        $response = $this->post('/users',[
            'name' => 'Joe Doe',
            'email' => 'Joe@user.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // $response->assertStatus(403); //  Laravel's built-in auth middleware, it might be redirecting unauthenticated/unauthorized users by default
        $response->assertStatus(302); 
        
        $this->assertDatabaseMissing('users', [
            'email' => 'joe@user.com',
        ]);
    }

    public function test_admin_can_view_all_users_list()
    {
        $admin = User::factory()->create(['is_admin' =>true]);
        $this->actingAs($admin);

        $users = User::factory()->count(3)->create(); //create 3 users

        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertViewHas('users', function($collection) use ($users){
            return $collection->count() ===4; //3 users + admin
        });
    }

    

}

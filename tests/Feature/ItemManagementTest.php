<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemManagementTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_an_authenticated_user_can_create_an_item(): void
    {
        //Any Authenticated User can create item
        // $user = User::factory()->create();
        // $this->actingAs($user);

        //Only Admin can Create Item
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);

        $response = $this->post('/items', [
            'name' => 'Laptop',
            'description' => 'A high end laptop',
            'quantity' => 10,
            'price' => 999.99,
        ]);

        $response->assertRedirect('/items');
        $this->assertDatabaseHas('items', [
            'name' => 'Laptop',
            'quantity' => 10,
        ]);
    }

    public function test_an_authenticated_user_can_view_the_items_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $items = Item::factory()->count(5)->create(); //create 5 items

        $response = $this->get('/items');

        $response->assertStatus(200);
        $response->assertViewIs('items.index');

        $response->assertViewHas('items', function ($collection) use ($items) {
            return $collection->count() === 5; //check there are 5 items
        });
    }

    public function test_an_authenticated_user_can_update_an_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'name' => 'Old Laptop',
            'quantity' => 3,
        ]);

        $response = $this->put("/items/{$item->id}", [
            'name' => 'New Laptop',
            'description' => 'Updated description',
            'price' => 1099.99,
            'quantity' => 15,

        ]);

        $response->assertRedirect('/items');
        $this->assertDatabaseHas('items',[
            'id' => $item->id,
            'name' => 'New Laptop',
            'quantity' => 15,
        ]);
    }

    public function test_an_authenticated_user_can_delete_an_item()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();

        $response = $this->delete("/items/{$item->id}");

        $response->assertRedirect('/items');

        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);
    }

    public function test_a_non_admin_cannot_create_an_item()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $response = $this->post("/items", [
            'name' => 'Laptop',
            'description' => 'A high end laptop',
            'quantity' => 10,
            'price' => 999.99,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('items', [
            'name' => 'Laptop'
        ]);
    }
}

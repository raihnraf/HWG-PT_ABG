<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;

class CategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_user_can_create_category()
    {
        $categoryData = [
            'name' => $this->faker->unique()->word,
        ];

        $response = $this->postJson('/api/v1/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'created_at', 'updated_at'],
            ]);

        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_user_can_get_all_categories()
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'created_at', 'updated_at'],
                ],
            ]);

        $this->assertEquals(5, count($response->json('data')));
    }

    public function test_user_can_get_single_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'created_at', 'updated_at'],
            ]);

        $this->assertEquals($category->name, $response->json('data.name'));
    }

    public function test_user_can_update_category()
    {
        $category = Category::factory()->create();
        $updatedData = ['name' => $this->faker->unique()->word];

        $response = $this->putJson("/api/v1/categories/{$category->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'created_at', 'updated_at'],
            ]);

        $this->assertDatabaseHas('categories', $updatedData);
    }

    public function test_user_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Category deleted successfully']);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}

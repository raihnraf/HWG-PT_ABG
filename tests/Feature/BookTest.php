<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;

class BookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker->seed(1234); // Set a fixed seed
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
        $this->category = Category::factory()->create();
    }

    public function test_user_can_create_book()
    {
        $totalCopies = $this->faker->numberBetween(1, 100);
        $bookData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'author' => $this->faker->name,
            'isbn' => $this->faker->unique()->isbn13,
            'published_year' => $this->faker->year,
            'category_id' => $this->category->id,
            'total_copies' => $totalCopies,
            'available_copies' => $this->faker->numberBetween(0, $totalCopies),
        ];

        $response = $this->postJson('/api/v1/books', $bookData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'description', 'author', 'isbn', 
                    'published_year', 'category_id', 'total_copies', 
                    'available_copies', 'created_at', 'updated_at'
                ],
            ]);

        $this->assertDatabaseHas('books', $bookData);
    }

    public function test_user_can_get_all_books()
    {
        Book::factory()->count(5)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'title', 'description', 'author', 'isbn', 
                        'published_year', 'category_id', 'total_copies', 
                        'available_copies', 'created_at', 'updated_at'
                    ]
                ]
            ]);

        $this->assertEquals(5, count($response->json('data')));
    }

    public function test_user_can_get_single_book()
    {
        $book = Book::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'description', 'author', 'isbn', 
                    'published_year', 'category_id', 'total_copies', 
                    'available_copies', 'created_at', 'updated_at'
                ],
            ]);

        $this->assertEquals($book->title, $response->json('data.title'));
    }

    public function test_user_can_update_book()
    {
        $book = Book::factory()->create(['category_id' => $this->category->id]);
        $updatedData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'author' => $this->faker->name,
            'isbn' => $this->faker->isbn13,
            'published_year' => $this->faker->year,
            'category_id' => $this->category->id,
            'total_copies' => $this->faker->numberBetween(1, 100),
            'available_copies' => $this->faker->numberBetween(0, 100), // Add this line
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'description', 'author', 'isbn', 
                    'published_year', 'category_id', 'total_copies', 
                    'available_copies', 'created_at', 'updated_at'
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => $updatedData['title'],
            'description' => $updatedData['description'],
            'author' => $updatedData['author'],
            'isbn' => $updatedData['isbn'],
            'published_year' => $updatedData['published_year'],
            'category_id' => $updatedData['category_id'],
            'total_copies' => $updatedData['total_copies'],
            'available_copies' => $updatedData['available_copies'],
        ]);
    }

    public function test_user_can_delete_book()
    {
        $book = Book::factory()->create(['category_id' => $this->category->id]);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Book deleted successfully']);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}

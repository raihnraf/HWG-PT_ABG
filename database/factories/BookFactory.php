<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalCopies = $this->faker->numberBetween(1, 100);
        
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'author' => $this->faker->name,
            'isbn' => $this->faker->isbn13,
            'published_year' => $this->faker->year,
            'category_id' => Category::factory(),
            'total_copies' => $totalCopies,
            'available_copies' => $this->faker->numberBetween(0, $totalCopies),
        ];
    }
}

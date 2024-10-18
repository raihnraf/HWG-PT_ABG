<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'author', 'isbn', 'published_year',
        'category_id', 'total_copies', 'available_copies'
    ];

    protected static function booted()
    {
        static::created(function ($book) {
            for ($i = 0; $i < $book->total_copies; $i++) {
                $book->copies()->create(['status' => 'available']);
            }
        });
    }

    /**
     * Get the category that owns the book.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the book copies for the book.
     */
    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    /**
     * Get the available copies of the book.
     */
    public function availableCopies()
    {
        return $this->copies()->where('status', 'available');
    }
}

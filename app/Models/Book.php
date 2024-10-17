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

    /**
     * Get the category that owns the book.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the book loans for the book.
     */
    public function bookLoans()
    {
        return $this->hasMany(BookLoan::class);
    }
}

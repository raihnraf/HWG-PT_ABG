<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'book_copy_id', 'borrowed_at', 'due_date', 'returned_at', 'status'
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_date' => 'date',
        'returned_at' => 'date',
    ];

    /**
     * Get the user that owns the book loan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book copy that is loaned.
     */
    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    /**
     * Get the book that is loaned.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_copy_id', 'id');
    }
}

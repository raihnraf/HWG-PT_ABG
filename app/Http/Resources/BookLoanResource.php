<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookLoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'book' => [
                'id' => $this->book->id,
                'title' => $this->book->title,
                'author' => $this->book->author,
            ],
            'borrowed_at' => $this->borrowed_at->toDateTimeString(),
            'due_date' => $this->due_date->toDateTimeString(),
            'returned_at' => $this->returned_at ? $this->returned_at->toDateTimeString() : null,
            'status' => $this->status,
        ];
    }
}

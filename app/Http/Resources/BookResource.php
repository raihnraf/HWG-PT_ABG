<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_year' => $this->published_year,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'total_copies' => $this->total_copies,
            'available_copies' => $this->available_copies,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

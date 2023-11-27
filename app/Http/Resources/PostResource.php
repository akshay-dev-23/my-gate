<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'content' => $this->content,
            'likes' => $this->likes ?? 0,
            'admin_notice' => $this->admin_notice,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'media' => $this->media
        ];
    }
}

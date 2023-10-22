<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'society_id' => $this->society_id,
            'flat_no' => $this->flat_no,
            'mobile_number' => $this->mobile_number,
            'verified' => $this->verified ? true : false
        ];
    }
}

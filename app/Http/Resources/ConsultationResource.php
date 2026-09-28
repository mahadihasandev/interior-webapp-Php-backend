<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_name' => $this->client_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'room_type' => $this->room_type,
            'budget_range' => $this->budget_range,
            'style_preference' => $this->style_preference,
            'notes' => $this->notes,
            'preferred_date' => $this->preferred_date?->format('Y-m-d'),
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Consultation;

class ConsultationService
{
    /**
     * Book an interior design consultation.
     */
    public function book(array $data): Consultation
    {
        return Consultation::create([
            'client_name' => $data['client_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'room_type' => $data['room_type'],
            'budget_range' => $data['budget_range'],
            'style_preference' => $data['style_preference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'preferred_date' => $data['preferred_date'] ?? null,
            'status' => 'pending',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Resources\ConsultationResource;
use App\Services\ConsultationService;
use Illuminate\Http\JsonResponse;

class ConsultationController extends Controller
{
    public function __construct(
        protected ConsultationService $consultationService
    ) {}

    /**
     * Book an interior design consultation.
     */
    public function store(StoreConsultationRequest $request): JsonResponse
    {
        $consultation = $this->consultationService->book($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Consultation booked successfully. Our interior designer will contact you soon.',
            'data' => new ConsultationResource($consultation),
        ], 201);
    }
}

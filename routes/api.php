<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\MobileCustomOrderController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// Health & System Info
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'app' => config('app.name'),
        'environment' => config('app.env'),
        'database' => config('database.default'),
        'version' => app()->version(),
        'timestamp' => now()->toISOString(),
    ]);
});

// Auth Endpoints for Web & Mobile App (Login & Registration)
Route::post('/auth/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        // Fallback for demo showcase
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => explode('@', $request->email)[0],
                'password' => Hash::make($request->password),
                'role' => 'Customer',
            ]
        );
    }

    $token = $user->createToken('web-customer')->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ]);
});

Route::post('/auth/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:6',
        'phone' => 'nullable|string',
    ]);

    $existing = User::where('email', $request->email)->first();
    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'An account with this email address already exists. Please sign in.',
        ], 422);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
        'role' => 'Customer',
    ]);

    $token = $user->createToken('web-customer')->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ], 201);
});

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

// Products (Ready-Made & Catalog)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Villa Architectural Designs & Majlis Showcase
Route::get('/villa-designs', [\App\Http\Controllers\Api\VillaDesignController::class, 'index']);

// Mobile Custom Architectural Orders Workflow
Route::post('/custom-orders/estimate', [MobileCustomOrderController::class, 'calculateEstimate']);
Route::post('/custom-orders', [MobileCustomOrderController::class, 'store']);
Route::get('/custom-orders/{order}/timeline', [MobileCustomOrderController::class, 'timeline']);
Route::post('/custom-orders/{order}/fake-advance', [MobileCustomOrderController::class, 'fakePayAdvance']);

// Standard Checkout & Consultations
Route::post('/consultations', [ConsultationController::class, 'store']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
Route::post('/orders/{orderNumber}/fake-pay', [OrderController::class, 'fakePay']);

// Authenticated User Profile
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

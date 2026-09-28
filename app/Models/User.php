<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'vendor_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function customOrders(): HasMany
    {
        return $this->hasMany(CustomOrder::class, 'customer_id');
    }

    // Role verification helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'SuperAdmin';
    }

    public function isVendorAdmin(): bool
    {
        return $this->role === 'VendorAdmin' || $this->isSuperAdmin();
    }

    public function isProductionManager(): bool
    {
        return in_array($this->role, ['ProductionManager', 'VendorAdmin', 'SuperAdmin']);
    }

    public function isSalesStaff(): bool
    {
        return in_array($this->role, ['SalesStaff', 'VendorAdmin', 'SuperAdmin']);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'Customer';
    }
}

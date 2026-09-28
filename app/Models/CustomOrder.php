<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'vendor_id',
        'product_id',
        'title',
        'dimensions',
        'material_specs',
        'color_finish',
        'addon_features',
        'customer_notes',
        'quoted_total_price',
        'advance_amount_required',
        'advance_paid_at',
        'full_paid_at',
        'estimated_completion_days',
        'current_stage',
        'status',
        'seller_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'material_specs' => 'array',
        'addon_features' => 'array',
        'quoted_total_price' => 'float',
        'advance_amount_required' => 'float',
        'advance_paid_at' => 'datetime',
        'full_paid_at' => 'datetime',
        'estimated_completion_days' => 'integer',
    ];

    public const STAGES = [
        'order_placed' => 'Order Placed & Submitted',
        'raw_material_sourcing' => 'Raw Material Sourcing',
        'cutting_welding' => 'Cutting & Structural Welding',
        'powder_coating' => 'Powder Coating & Anodizing',
        'assembly_glass_fitting' => 'Assembly & Glass Fitting',
        'quality_check' => 'Engineering Quality Check',
        'ready_for_dispatch' => 'Ready for Dispatch',
        'delivered' => 'Delivered & Installed',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(OrderTimelineEvent::class)->orderBy('created_at', 'asc');
    }

    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->current_stage] ?? ucfirst(str_replace('_', ' ', $this->current_stage));
    }

    public function isAdvancePaid(): bool
    {
        return $this->advance_paid_at !== null;
    }

    public function isPendingReview(): bool
    {
        return $this->status === 'pending_review';
    }

    public function isAccepted(): bool
    {
        return in_array($this->status, ['accepted', 'in_production', 'ready', 'completed']);
    }
}

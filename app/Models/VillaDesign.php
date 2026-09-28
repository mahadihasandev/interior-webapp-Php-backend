<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaDesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'type',
        'category_key',
        'category_name_en',
        'category_name_ar',
        'title_en',
        'title_ar',
        'tagline',
        'location_tag',
        'photo_url',
        'detail_photo_url',
        'price_sar',
        'price_usd',
        'advance_deposit_sar',
        'advance_deposit_usd',
        'features',
        'specs',
        'config_data',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'specs' => 'array',
        'config_data' => 'array',
        'price_sar' => 'float',
        'price_usd' => 'float',
        'advance_deposit_sar' => 'float',
        'advance_deposit_usd' => 'float',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Default room categories with dual language titles
     */
    public static function defaultCategories(): array
    {
        return [
            'majlis' => [
                'name_en' => 'Royal Majlis & Salons',
                'name_ar' => 'المجالس وصالونات الاستقبال',
            ],
            'thermal_window' => [
                'name_en' => '50°C Thermal Windows',
                'name_ar' => 'نوافذ العزل الحراري (مقاومة 50°م)',
            ],
            'privacy_partition' => [
                'name_en' => 'Privacy & Mashrabiya',
                'name_ar' => 'فواصل الخصوصية والمشربية',
            ],
            'family_living' => [
                'name_en' => 'Family Living Lounges',
                'name_ar' => 'صالات المعيشة العائلية',
            ],
        ];
    }
}

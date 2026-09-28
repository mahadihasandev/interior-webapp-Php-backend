<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillaDesign;
use Illuminate\Http\JsonResponse;

class VillaDesignController extends Controller
{
    public function index(): JsonResponse
    {
        $designs = VillaDesign::where('is_active', true)
            ->orderBy('sort_order')
            ->latest('id')
            ->get();

        // Format to match frontend CustomSample interface seamlessly
        $formatted = $designs->map(function ($d) {
            return [
                'id'                => $d->slug,
                'dbId'              => $d->id,
                'type'              => $d->type,
                'titleEn'           => $d->title_en,
                'titleAr'           => $d->title_ar,
                'tagline'           => $d->tagline,
                'roomCategory'      => $d->category_key,
                'roomCategoryLabel' => $d->category_name_en,
                'categoryNameAr'    => $d->category_name_ar,
                'locationTag'       => $d->location_tag,
                'photoUrl'          => $d->photo_url,
                'detailPhotoUrl'    => $d->detail_photo_url ?: $d->photo_url,
                'priceUSD'          => (float)$d->price_usd,
                'priceSAR'          => (float)$d->price_sar,
                'advanceDepositUSD' => (float)$d->advance_deposit_usd,
                'advanceDepositSAR' => (float)$d->advance_deposit_sar,
                'saudiFeatures'     => $d->features ?: [],
                'specs'             => $d->specs ?: [
                    'dimensions'     => 'Custom',
                    'finishOrFabric' => 'Architectural Grade',
                    'coreMaterial'   => 'Structural Alloy',
                    'hardware'       => 'Concealed Pivots',
                ],
                'configData'        => $d->config_data ?: [],
            ];
        });

        // Unique category tabs for frontend navigation
        $categories = $designs->map(function ($d) {
            return [
                'id'     => $d->category_key,
                'nameEn' => $d->category_name_en,
                'nameAr' => $d->category_name_ar,
            ];
        })->unique('id')->values()->all();

        return response()->json([
            'success'    => true,
            'data'       => $formatted,
            'categories' => $categories,
        ]);
    }
}

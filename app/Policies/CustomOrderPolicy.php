<?php

namespace App\Policies;

use App\Models\CustomOrder;
use App\Models\User;

class CustomOrderPolicy
{
    /**
     * Determine if the user can view the custom order.
     */
    public function view(User $user, CustomOrder $order): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isCustomer()) {
            return $order->customer_id === $user->id;
        }

        // Vendor staff can only view orders assigned to their vendor
        return $user->vendor_id === $order->vendor_id;
    }

    /**
     * Determine if the user can evaluate, quote, accept, or reject the order.
     */
    public function manage(User $user, CustomOrder $order): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return ($user->isVendorAdmin() || $user->isSalesStaff()) &&
               $user->vendor_id === $order->vendor_id;
    }

    /**
     * Determine if the user can update the manufacturing stage and post timeline evidence.
     */
    public function updateProgress(User $user, CustomOrder $order): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isProductionManager() &&
               $user->vendor_id === $order->vendor_id;
    }
}

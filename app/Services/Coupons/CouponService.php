<?php

namespace WebbyCrown\CommerceSuite\Services\Coupons;

use Statamic\Facades\Entry;

class CouponService
{
    public function findValid(string $code, float $subtotal): ?array
    {
        $code = strtoupper(trim($code));

        $entry = Entry::query()
                ->where('collection', 'coupons')   // ← replace whereCollection()
                ->where('code', $code)
                ->where('active', true)
                ->first();

        if (! $entry) {
            return null;
        }

        // Expiry check
        $expiresAt = $entry->get('expires_at');
        if ($expiresAt && now()->gt($expiresAt)) {
            return null;
        }

        $minAmount = (float) ($entry->get('min_amount') ?? 0);
        if ($subtotal < $minAmount) {
            return null;
        }

        return [
            'coupon_id'     => $entry->id(),
            'code'          => $code,
            'discount_type' => $entry->get('discount_type') ?? 'fixed',
            'discount_value'=> (float) ($entry->get('discount_value') ?? 0),
            'min_amount'    => $minAmount,
        ];
    }

    public function calculateDiscount(array $coupon, float $subtotal): float
    {
        if ($subtotal < $coupon['min_amount']) {
            return 0;
        }

        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($subtotal * $coupon['discount_value']) / 100;
        } else {
            $discount = $coupon['discount_value'];
        }

        return min($discount, $subtotal);
    }
}

<?php

namespace App\Services\Coupons;

use Statamic\Facades\Entry;

class CouponService
{
    public function findValid(string $code, float $subtotal): ?array
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return null;
        }

        $entry = Entry::query()
            ->where('collection', 'coupons')
            ->where('code', $code)
            ->first();

        if (! $entry) {
            return null;
        }

        $min = (float) ($entry->get('min_amount') ?? 0);
        if ($subtotal < $min) {
            return null;
        }

        $expires = $entry->get('expires_at');
        if ($expires && strtotime((string) $expires) < time()) {
            return null;
        }

        return [
            'coupon_id' => $entry->id(),
            'code' => $entry->get('code'),
            'discount_type' => $entry->get('discount_type') ?: 'fixed',
            'discount_value' => (float) $entry->get('discount_value'),
        ];
    }

    public function calculateDiscount(array $coupon, float $subtotal): float
    {
        $value = (float) ($coupon['discount_value'] ?? 0);
        if (($coupon['discount_type'] ?? 'fixed') === 'percentage') {
            return round(min($subtotal, $subtotal * ($value / 100)), 2);
        }

        return round(min($subtotal, $value), 2);
    }
}

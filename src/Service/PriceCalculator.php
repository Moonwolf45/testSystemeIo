<?php

namespace App\Service;

class PriceCalculator
{
    private array $taxRates = [
        'DE' => 19,
        'IT' => 22,
        'FR' => 20,
        'GR' => 24
    ];

    public function calculate(float $basePrice, string $taxNumber, ?array $coupon = null): float
    {
        $country = substr($taxNumber, 0, 2);
        $taxRate = $this->taxRates[$country] ?? 0;

        $priceAfterDiscount = $basePrice;
        if ($coupon) {
            if ($coupon['type'] === 'fixed') {
                $priceAfterDiscount = max(0, $basePrice - $coupon['value']);
            } elseif ($coupon['type'] === 'percent') {
                $priceAfterDiscount = $basePrice * (1 - $coupon['value'] / 100);
            }
        }

        return round($priceAfterDiscount * (1 + $taxRate / 100), 2);
    }
}
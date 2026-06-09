<?php

namespace App\Application\Services;

class PriceCalculationService
{
    public function calculateAverage(array $prices): float
    {
        if (empty($prices)) {
            return 0.0;
        }

        return array_sum($prices) / count($prices);
    }

    public function calculateTrend(array $prices): string
    {
        if (count($prices) < 2) {
            return 'stable';
        }

        // Ensure chronological order (oldest first)
        $prices = array_reverse($prices);

        $first = $prices[0];
        $last = $prices[count($prices) - 1];
        $difference = $last - $first;
        $threshold = $first * 0.01; // 1% threshold

        if ($difference > $threshold) {
            return 'up';
        } elseif ($difference < -$threshold) {
            return 'down';
        }

        return 'stable';
    }

    public function formatPrice(float $amount): string
    {
        $formatted = number_format($amount, 0, ',', '.');
        return "Rp {$formatted}";
    }
}

<?php

use Illuminate\Support\Number;

if (! function_exists('format_money')) {
    /**
     * Format a decimal amount for a given currency.
     */
    function format_money(float|int|string|null $amount, string $currency = 'IDR'): string
    {
        $amount = (float) $amount;

        if (strtoupper($currency) === 'IDR') {
            return 'Rp '.Number::format($amount, maxPrecision: 0);
        }

        return Number::currency($amount, in: strtoupper($currency));
    }
}

if (! function_exists('format_date')) {
    function format_date(DateTimeInterface|string|null $date): string
    {
        if ($date === null) {
            return '';
        }

        if (is_string($date)) {
            $date = new DateTimeImmutable($date);
        }

        return $date->format('d M Y');
    }
}

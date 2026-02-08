<?php

namespace App\Helpers;

class CurrencyConverter
{
    /**
     * Default rate: 1 USD = 59 PHP
     */
    public const DEFAULT_USD_TO_PHP = 59;

    /**
     * Convert USD to PHP.
     */
    public static function usdToPhp(float $amount, ?float $rate = null): float
    {
        $rate = $rate ?? (float) self::DEFAULT_USD_TO_PHP;
        return round($amount * $rate, 2);
    }

    /**
     * Convert PHP to USD.
     */
    public static function phpToUsd(float $amount, ?float $rate = null): float
    {
        $rate = $rate ?? (float) self::DEFAULT_USD_TO_PHP;
        return round($amount / $rate, 2);
    }

    /**
     * Get the display equivalent when showing in the other currency (USD ⇄ PHP).
     * Returns e.g. ['symbol' => '₱', 'amount' => 5900.00] for 100 USD at rate 59.
     */
    public static function equivalentAmount(float $amount, string $currentSymbol, ?float $rate = null): ?array
    {
        $rate = $rate ?? (float) self::DEFAULT_USD_TO_PHP;

        if ($currentSymbol === '$') {
            return ['symbol' => '₱', 'amount' => self::usdToPhp($amount, $rate)];
        }
        if ($currentSymbol === '₱') {
            return ['symbol' => '$', 'amount' => self::phpToUsd($amount, $rate)];
        }

        return null;
    }
}

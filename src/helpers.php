<?php

if (! function_exists('ceil_plus')) {
    /**
     * Ceil with precision
     * @param float $value
     * @param int|null $precision
     * @return float
     */
    function ceil_plus(float $value, ?int $precision = null): float
    {
        if (null === $precision) {
            return (float)ceil($value);
        }
        if ($precision < 0) {
            throw new \RuntimeException('Invalid precision');
        }

        $reg = $value + 0.5 / (10 ** $precision);
        return round($reg, $precision, $reg > 0 ? PHP_ROUND_HALF_DOWN : PHP_ROUND_HALF_UP);
    }
}

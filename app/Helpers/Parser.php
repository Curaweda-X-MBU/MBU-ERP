<?php

namespace App\Helpers;

class Parser
{
    /**
     * Parse locale string id-ID to float value
     *
     * @param  null|string  $value
     * @return float
     */
    public static function parseLocale($value)
    {
        if (is_null($value)) {
            return 0;
        }

        // $value = str_replace(',', '', $value);
        $value = str_replace('.', '_', $value);
        $value = str_replace(',', '.', $value);
        $value = str_replace('_', '', $value);

        return floatval($value) ?: 0;
    }

    /**
     * Parse locale string id-ID to float value
     *
     * @param  float  $value
     * @return string
     */
    public static function toLocale($value)
    {
        return number_format($value, 2, ',', '.');
    }

    /**
     * Parse locale string id-ID to float value and trim excess 0 decimal
     *
     * @param  float  $value
     * @return string
     */
    public static function trimLocale($value)
    {
        $locale   = number_format($value, 2, ',', '.');
        $exploded = explode(',', $locale);

        return $exploded[1] == '00' ? $exploded[0] : $locale;
    }
}

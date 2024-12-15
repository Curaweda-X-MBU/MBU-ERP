<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Encrypt data using Laravel's Crypt facade.
 *
 * @param  string  $data
 * @return string
 */
function encryptData($data)
{
    return Crypt::encrypt($data);
}

/**
 * Decrypt data using Laravel's Crypt facade.
 *
 * @param  string  $encryptedData
 * @return string
 *
 * @throws DecryptException
 */
function decryptData($encryptedData)
{
    try {
        return Crypt::decrypt($encryptedData);
    } catch (DecryptException $e) {
        return false;
    }
}

/**
 * Parse locale string id-ID to float value
 *
 * @param  string  $data
 * @return string
 */
function parseLocale(?string $value): float
{
    if (is_null($value)) {
        return 0;
    }

    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);

    return floatval($value) ?: 0;
}

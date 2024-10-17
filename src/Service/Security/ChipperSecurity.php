<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ChipperSecurity.php
 * Updated At: 17.10.2024, 13:50
 *
 */

namespace SormModule\Service\Security;

use Random\RandomException;
use SormModule\Sorm;

final class ChipperSecurity extends SormService
{
    private static $settings;

    public function __construct()
    {
        self::$settings = Sorm::loadSettings();
        if (empty(self::$settings['APP_KEY'])) {
            self::generateAppKey();
        }
    }

    public static function encrypt(mixed $data)
    {
        $appKey = self::$settings['APP_KEY'];
        $rounds = self::$settings['CHIPPER_ROUNDS'];

        $encryptedData = $data;
        for ($i = 0; $i < $rounds; $i++) {
            $encryptedData = openssl_encrypt($encryptedData, 'aes-256-cbc', $appKey, 0, substr($appKey, 0, 16));
        }

        return $encryptedData;
    }

    public static function decrypt(mixed $data)
    {
        $appKey = self::$settings['APP_KEY'];
        $rounds = self::$settings['CHIPPER_ROUNDS'];

        $decryptedData = $data;
        for ($i = 0; $i < $rounds; $i++) {
            $decryptedData = openssl_decrypt($decryptedData, 'aes-256-cbc', $appKey, 0, substr($appKey, 0, 16));
        }

        return $decryptedData;
    }

    /**
     * @throws RandomException
     * @throws \Exception
     */
    public static function generateAppKey(): string
    {
        self::$settings = Sorm::loadSettings();

        $appKey = self::generateRandomBytes(128);
        self::$settings['APP_KEY'] = $appKey;
        Sorm::saveSettings(self::$settings);
        return $appKey;
    }

    /**
     * @throws RandomException
     */
    public static function generateRandomBytes(int $length): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * @throws \Exception
     */
    public static function encryptDb(): void
    {
        self::$settings = Sorm::loadSettings();

        if (isset(self::$settings['database'])) {
            self::$settings['database']['host']     = self::encrypt(self::$settings['database']['host']);
            self::$settings['database']['name']     = self::encrypt(self::$settings['database']['name']);
            self::$settings['database']['user']     = self::encrypt(self::$settings['database']['user']);
            self::$settings['database']['password'] = self::encrypt(self::$settings['database']['password']);
            self::$settings['database']['port']     = self::encrypt(self::$settings['database']['port']);
        }


        if (isset(self::$settings['paymmentMethods'])) {
            self::$settings['paymmentMethods']['host']     = self::encrypt(self::$settings['paymmentMethods']['host']);
            self::$settings['paymmentMethods']['name']     = self::encrypt(self::$settings['paymmentMethods']['name']);
            self::$settings['paymmentMethods']['user']     = self::encrypt(self::$settings['paymmentMethods']['user']);
            self::$settings['paymmentMethods']['password'] = self::encrypt(self::$settings['paymmentMethods']['password']);
            self::$settings['paymmentMethods']['port']     = self::encrypt(self::$settings['paymmentMethods']['port']);
        }

        Sorm::saveSettings(self::$settings);
    }

}
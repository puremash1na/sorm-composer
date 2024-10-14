<?php
/*
 * Copyright (c) 2024 - 2024, Webhost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 14.10.2024, 20:42
 */

namespace SormModule\Service;

use Exception;
use SormModule\Service\Api\ApiSormService;
use SormModule\Sorm;

final class ApiSorm
{
    /**
     * @throws Exception
     */
    private static string $settings;
    private static string $db;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        self::$db       = Sorm::initDatabase(); // получили БД
        self::$settings = Sorm::loadSettings(); // получили настройки
    }

    /**
     * @throws Exception
     */
    public static function exportToSorm(int $bathSize): void
    {
        try {
            ApiSormService::sendRequest(
                self::$settings['sormApiUrl'],
                'POST',
                ['exportToSormFromBilling' => time()]
            );
        } catch (Exception $exception) {
            Sorm::log("Error exporting to sorm service: " . $exception->getMessage());
        }
    }
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists(__CLASS__, $name)) {
            return forward_static_call_array([__CLASS__, $name], $arguments);
        }

        throw new \BadMethodCallException("Method {$name} does not exist");
    }
    public static function call(string $method, ?array $arguments = [])
    {
        return call_user_func_array([__CLASS__, $method], $arguments);
    }
}
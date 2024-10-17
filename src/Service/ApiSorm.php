<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 17.10.2024, 13:31
 *
 */

namespace SormModule\Service;

use Exception;
use SormModule\Service\Api\ApiSormService;
use SormModule\Service\Security\SormService;
use SormModule\Sorm;

final class ApiSorm extends SormService
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
    public static function exportToSorm(array $data): void
    {
        try {
            ApiSormService::sendRequest(
                self::$settings['sormApiUrl'].'/exportToSorm',
                'POST',
                ['exportToSormFromBilling' => time()],
                $data,
                $data
            );
        } catch (Exception $exception) {
            Sorm::log("Error exporting to sorm service: " . $exception->getMessage());
        }
    }
}
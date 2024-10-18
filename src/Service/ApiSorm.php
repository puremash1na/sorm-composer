<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 18.10.2024, 14:45
 *
 */

namespace SormModule\Service;

use Exception;
use SormModule\Installer;
use SormModule\Service\Security\ChipperSecurity;
use SormModule\Service\Security\SormService;
use SormModule\Sorm;

final class ApiSorm extends SormService
{
    /**
     * @throws Exception
     */
    private static $settings;
    private static $db;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        self::$db       = Sorm::initDatabase();         // получили БД
        self::$settings = ChipperSecurity::decryptDb(); // получили настройки
    }
    public static function transformObject()
    {
        $associationsDb   = self::$settings['associationsDb'];
        $associationsKeys = self::$settings['associationsKeys'];
        foreach ($associationsDb as $logicalTableName => $dbConfig) {
            $dbType = key($dbConfig);
            $tableName = $dbConfig[$dbType];

            $dbCreds = $settings[$dbType] ?? null;

            if (!$dbCreds) {
                echo "[Error] Креды для базы данных {$dbType} не найдены.\n";
                continue;
            }

            $database = Installer::initDatabaseConnection($dbCreds);

            $keys = $associationsKeys[$logicalTableName] ?? null;
            if (!$keys) {
                continue;
            }

            foreach ($keys as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey) {
                        if ($subKey === null || $subKey === '') {
                            continue;
                        }
                        echo "63: Обращаемся к $tableName.$subKey\n";
                    }
                } else {
                    if($value === null || $value === '') {
                        continue;
                    }
                    echo "69: Обращаемся к $tableName.$value\n";
                }
            }
        }
    }
}
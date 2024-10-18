<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 18.10.2024, 15:01
 *
 */

namespace SormModule\Service;

use Exception;
use SormModule\Installer;
use SormModule\Service\Security\ChipperSecurity;
use SormModule\Service\Security\SormService;
final class ApiSorm extends SormService
{

    /**
     * @throws Exception
     */
    public static function transformObject()
    {
        $settings         = ChipperSecurity::decryptDb();
        $associationsDb   = $settings['associationsDb'];
        $associationsKeys = $settings['associationsKeys'];

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

            echo "Обращаемся к БД: $tableName\n";

            // Выполнение запроса для получения общего количества элементов
            try {
                $query = "SELECT COUNT(*) FROM `$tableName`";
                $stmt = $database->prepare($query);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count !== false) {
                    echo "Общее количество элементов в $tableName: $count\n";
                } else {
                    echo "[Error] Не удалось получить количество элементов для таблицы $tableName.\n";
                }
            } catch (\PDOException $e) {
                echo "[Error] Ошибка выполнения запроса для таблицы $tableName: " . $e->getMessage() . "\n";
            }
        }
    }


}
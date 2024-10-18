<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 18.10.2024, 15:07
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

            echo "Обращаемся к БД: $tableName в СОРМЕ: $dbType\n";
//
//            try {
//                $query = "SELECT * FROM `$tableName`";
//                $stmt = $database->prepare($query);
//                $stmt->execute();
//                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//                $batchSize = 100;
//                $totalCount = count($data);
//                for ($i = 0; $i < $totalCount; $i += $batchSize) {
//                    $batch = array_slice($data, $i, $batchSize);
//
//                    // Здесь отправляем пакет на API
//                    $response = self::sendToApi($batch);
//
//                    if (!$response) {
//                        echo "[Error] Ошибка при отправке данных на API.\n";
//                    } else {
//                        echo "Отправлено " . count($batch) . " элементов на API.\n";
//                    }
//
//                    // Добавляем задержку
//                    sleep(1); // Задержка в 1 секунду (можно настроить по необходимости)
//                }
//            } catch (\PDOException $e) {
//                echo "[Error] Ошибка выполнения запроса для таблицы $tableName: " . $e->getMessage() . "\n";
//            }
        }
    }
}
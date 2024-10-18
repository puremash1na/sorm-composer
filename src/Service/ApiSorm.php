<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 18.10.2024, 16:09
 *
 */

namespace SormModule\Service;

use Exception;
use PDO;
use SormModule\Installer;
use SormModule\Service\DTO\Database\LogIs;
use SormModule\Service\DTO\Database\Operation;
use SormModule\Service\DTO\Database\Order;
use SormModule\Service\DTO\Database\PaymentMethod;
use SormModule\Service\DTO\Database\Person;
use SormModule\Service\DTO\Database\Tariff;
use SormModule\Service\DTO\Database\Ticket;
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

        // Массивы для хранения объектов
        $logIsObjects          = [];
        $operationsObjects     = [];
        $ordersObjects         = [];
        $paymentMethodsObjects = [];
        $personObjects         = [];
        $tariffObjects         = [];
        $ticketsObjects        = [];

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

            echo "Обращаемся к БД: $tableName в СОРМЕ: $logicalTableName\n";
            try {
                $countQuery = "SELECT COUNT(*) FROM `$tableName`";
                $countStmt = $database->prepare($countQuery);
                $countStmt->execute();
                $totalCount = $countStmt->fetchColumn();

                // Получаем информацию о размере таблицы
                $sizeQuery = "SHOW TABLE STATUS LIKE '$tableName'";
                $sizeStmt = $database->prepare($sizeQuery);
                $sizeStmt->execute();
                $tableStatus = $sizeStmt->fetch(PDO::FETCH_ASSOC);
                $dataSizeMB = isset($tableStatus['Data_length']) ? $tableStatus['Data_length'] / (1024 * 1024) : 0;

                $query = "SELECT * FROM `$tableName`";
                $stmt = $database->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $start = microtime(true);

                foreach ($data as $row) {
                    switch ($logicalTableName) {
                        case 'logs_is':
                            $params = [];
                            foreach ($keys as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $subKey) {
                                        $params[] = $row[$subKey];
                                    }
                                } else {
                                    $params[] = $row[$value];
                                }
                            }
                            $logIsObjects[] = new LogIs(...$params);
                            break;
                        case 'operations':
                            $params = [];
                            foreach ($keys as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $subKey) {
                                        $params[] = $row[$subKey];
                                    }
                                } else {
                                    $params[] = $row[$value];
                                }
                            }
                            $operationsObjects[] = new Operation(...$params);
                            break;
                        case 'orders':
                            $params = [];
                            foreach ($keys as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $subKey) {
                                        $params[] = $row[$subKey];
                                    }
                                } else {
                                    $params[] = $row[$value];
                                }
                            }
                            $ordersObjects[] = new Order(...$params); // Передаём параметры
                            break;
                        case 'payment_methods':
                            $params = [];
                            foreach ($keys as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $subKey) {
                                        $params[] = $row[$subKey];
                                    }
                                } else {
                                    $params[] = $row[$value];
                                }
                            }
                            $paymentMethodsObjects[] = new PaymentMethod(...$params); // Передаём параметры
                            break;
                        case 'person':
                            $params = [];
                            foreach ($keys as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $subKey) {
                                        $params[] = $row[$subKey];
                                    }
                                } else {
                                    $params[] = $row[$value];
                                }
                            }
                            $personObjects[] = new Person(...$params); // Передаём параметры
                            break;
                        case 'tariffs':
                            $params = [];
                            foreach ($keys as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $subKey) {
                                        $params[] = $row[$subKey];
                                    }
                                } else {
                                    $params[] = $row[$value];
                                }
                            }
                            $tariffObjects[] = new Tariff(...$params); // Передаём параметры
                            break;
                        case 'tickets':
                            $params = [];
                            foreach ($keys as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $subKey) {
                                        $params[] = $row[$subKey];
                                    }
                                } else {
                                    $params[] = $row[$value];
                                }
                            }
                            $ticketsObjects[] = new Ticket(...$params); // Передаём параметры
                            break;
                    }
                 }
                $end = microtime(true);
                $executionTime = $end - $start;
                echo "Время выполнения для таблицы $tableName: Общее количество записей: $totalCount / Общий размер БД (мб): " . number_format($dataSizeMB, 2) . " : " . number_format($executionTime, 4) . " секунд\n";
            } catch (\PDOException $e) {
                echo "[Error] Ошибка выполнения запроса для таблицы $tableName: " . $e->getMessage() . "\n";
            }
        }
    }

}
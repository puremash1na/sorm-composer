<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 18.10.2024, 16:37
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
    public static function transformObject(): void
    {
        // Начало отслеживания времени
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $finish = [
            'person'          => false,
            'tariffs'         => false,
            'tickets'         => false,
            'payment_methods' => false,
            'orders'          => false,
            'operations'      => false,
            'logs_is'         => false,
        ];

        $settings = ChipperSecurity::decryptDb();

        $associationsDb   = $settings['associationsDb'];
        $associationsKeys = $settings['associationsKeys'];

        $logIsObjects          = [];
        $operationsObjects     = [];
        $ordersObjects         = [];
        $paymentMethodsObjects = [];
        $personObjects         = [];
        $tariffObjects         = [];
        $ticketsObjects        = [];

        foreach ($associationsDb as $logicalTableName => $dbConfig) {
            $dbType    = key($dbConfig);
            $tableName = $dbConfig[$dbType];

            $dbCreds = $settings[$dbType] ?? null;

            if (!$dbCreds) {
                echo "[Error] Креды для базы данных {$dbType} не найдены.\n";
                continue;
            }

            $database = Installer::initDatabaseConnection($dbCreds);
            $keys     = $associationsKeys[$logicalTableName] ?? null;

            if (!$keys) {
                continue;
            }

            if ($finish[$logicalTableName]) {
                echo "Таблица $logicalTableName уже обработана.\n";
                continue;
            }

            echo "Обращаемся к БД: $tableName в СОРМЕ: $logicalTableName\n";

            try {
                $countQuery = "SELECT COUNT(*) FROM `$tableName`";
                $countStmt  = $database->prepare($countQuery);
                $countStmt->execute();

                $totalCount     = $countStmt->fetchColumn();
                $batchSize      = min($totalCount, $settings['exportStep']);
                $processedCount = 0;

                while ($processedCount < $totalCount) {
                    $query = "SELECT * FROM `$tableName` LIMIT $batchSize OFFSET $processedCount";
                    $stmt = $database->prepare($query);
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($data)) {
                        break;
                    }

                    foreach ($data as $row) {
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

                        switch ($logicalTableName) {
                            case 'logs_is':
                                $logIsObjects[] = new LogIs(...$params);
                                break;
                            case 'operations':
                                $operationsObjects[] = new Operation(...$params);
                                break;
                            case 'orders':
                                $ordersObjects[] = new Order(...$params);
                                break;
                            case 'payment_methods':
                                $paymentMethodsObjects[] = new PaymentMethod(...$params);
                                break;
                            case 'person':
                                $personObjects[] = new Person(...$params);
                                break;
                            case 'tariffs':
                                $tariffObjects[] = new Tariff(...$params);
                                break;
                            case 'tickets':
                                $ticketsObjects[] = new Ticket(...$params);
                                break;
                        }
                    }

                    $processedCount += count($data);
                    echo "Обработано $processedCount из $totalCount для таблицы $tableName.\n";
                }
                $finish[$logicalTableName] = true;
                echo "Таблица $logicalTableName полностью обработана.\n";

            } catch (\PDOException $e) {
                echo "[Error] Ошибка выполнения запроса для таблицы $tableName: " . $e->getMessage() . "\n";
            }
        }

        $endTime   = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = $endTime - $startTime;
        $memoryUsage   = $endMemory - $startMemory;

        echo "Общее время выполнения скрипта: " . number_format($executionTime, 2) . " секунд\n";
        echo "Общее использование памяти: " . round($memoryUsage / 1024, 2) . " КБ\n";
    }
}
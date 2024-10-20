<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 21.10.2024, 21:58
 *
 */

namespace SormModule\Service;

use Exception;
use PDO;
use SormModule\Installer;
use SormModule\Service\Api\ApiSormService;
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

        foreach ($associationsDb as $logicalTableName => $dbConfig) {
            $dbType    = key($dbConfig); // database or paymentMethods
            $tableName = $dbConfig[$dbType]; // logs
            $dbCreds = $settings[$dbType] ?? null;
            if($tableName === 'tickets' || $tableName === 'person') {
                continue;
            }
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
                    // Формируем запрос с выборкой только нужных полей
                    $selectedFields = [];
                    foreach ($keys as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $subKey) {
                                $selectedFields[] = "`$subKey`";
                            }
                        } else {
                            $selectedFields[] = "`$value`";
                        }
                    }
                    $query = "SELECT " . implode(', ', $selectedFields) . " FROM `$tableName` LIMIT $batchSize OFFSET $processedCount";
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
                                $object = new LogIs(...$params);
                                break;
                            case 'operations':
                                $object = new Operation(...$params);
                                break;
                            case 'orders':
                                $object = new Order(...$params);
                                break;
                            case 'payment_methods':
                                $object = new PaymentMethod(...$params);
                                break;
                            case 'tariffs':
                                $object = new Tariff(...$params);
                                break;
                        }

                        // Теперь сразу отправляем данные на API
                        if (isset($object)) {
                            $exportData = $object->dataForExport(); // или $object->dataForExport();
                            $object = null;
                            ApiSormService::exportToSorm($settings['sormApiUrl'], $settings['APP_KEY'], $exportData);
                            echo "Объект для таблицы $logicalTableName отправлен на API.\n";
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
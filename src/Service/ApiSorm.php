<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 18.10.2024, 15:22
 *
 */

namespace SormModule\Service;

use Exception;
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
                $query = "SELECT * FROM `$tableName`";
                $stmt = $database->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Ограничиваем количество элементов для обработки
                $data = array_slice($data, 0, 100);
                $totalCount = count($data);

                foreach ($data as $row) {
                    switch ($logicalTableName) {
                        case 'logs_is':
                            $logIsObjects[] = new LogIs(
                                $row[$keys[0]] ?? null,
                                $row[$keys[1]] ?? null,
                                $row[$keys[2]] ?? null,
                                $row[$keys[3]] ?? null,
                                $row[$keys[4]] ?? false,
                                $row[$keys[5]] ?? null,
                                $row[$keys[6]] ?? null
                            );
                            break;
                        case 'operations':
                            $operationsObjects[] = new Operation(
                                $row[$keys[0]] ?? null,
                                $row[$keys[1]] ?? null,
                                $row[$keys[2]] ?? null,
                                $row[$keys[3]] ?? null,
                                $row[$keys[4]] ?? false,
                                $row[$keys[5]] ?? null,
                                $row[$keys[6]] ?? null,
                                $row[$keys[7]] ?? null,
                                $row[$keys[8]] ?? null,
                                $row[$keys[9]] ?? null,
                                $row[$keys[10]] ?? null,
                                $row[$keys[11]] ?? null,
                                $row[$keys[12]] ?? null,
                                $row[$keys[13]] ?? null,
                            );
                            break;
                        case 'orders':
                            $ordersObjects[] = new Order(
                                $row[$keys[0]] ?? null,
                                $row[$keys[1]] ?? null,
                                $row[$keys[2]] ?? null,
                                $row[$keys[3]] ?? null,
                                $row[$keys[4]] ?? false,
                                $row[$keys[5]] ?? null,
                                $row[$keys[6]] ?? null,
                                $row[$keys[7]] ?? null,
                                $row[$keys[8]] ?? null,
                                $row[$keys[9]] ?? null,
                                $row[$keys[10]] ?? null,
                                $row[$keys[11]] ?? null,
                                $row[$keys[12]] ?? null,
                                $row[$keys[13]] ?? null,
                                $row[$keys[14]] ?? null,
                                $row[$keys[15]] ?? null,
                            );
                            break;
                        case 'payment_methods':
                            $paymentMethodsObjects[] = new PaymentMethod(
                                $row[$keys[0]] ?? null,
                                $row[$keys[1]] ?? null,
                                $row[$keys[2]] ?? null,
                                $row[$keys[3]] ?? null,
                                $row[$keys[4]] ?? false,
                                $row[$keys[5]] ?? null,
                            );
                            break;
                        case 'person':
                            $personObjects[] = new Person(
                                $row[$keys[0]] ?? null,
                                $row[$keys[1]] ?? null,
                                $row[$keys[2]] ?? null,
                                $row[$keys[3]] ?? null,
                                $row[$keys[4]] ?? false,
                                $row[$keys[5]] ?? null,
                                $row[$keys[6]] ?? null,
                                $row[$keys[7]] ?? null,
                                $row[$keys[8]] ?? null,
                                $row[$keys[9]] ?? null,
                                $row[$keys[10]] ?? null,
                                $row[$keys[11]] ?? null,
                                $row[$keys[12]] ?? null,
                                $row[$keys[13]] ?? null,
                                $row[$keys[14]] ?? null,
                                $row[$keys[15]] ?? null,
                                $row[$keys[16]] ?? null,
                                $row[$keys[17]] ?? null,
                                $row[$keys[18]] ?? null,
                                $row[$keys[19]] ?? null,
                                $row[$keys[20]] ?? null,
                                $row[$keys[21]] ?? null,
                                $row[$keys[22]] ?? null,
                                $row[$keys[23]] ?? null,
                                $row[$keys[24]] ?? null,
                            );
                            break;
                        case 'tariffs':
                            $tariffObjects[] = new Tariff(
                                $row[$keys[0]] ?? null,
                                $row[$keys[1]] ?? null,
                                $row[$keys[2]] ?? null,
                                $row[$keys[3]] ?? null,
                                $row[$keys[4]] ?? false,
                                $row[$keys[5]] ?? null,
                                $row[$keys[6]] ?? null
                            );
                            break;
                        case 'tickets':
                            $ticketsObjects[] = new Ticket(
                                $row[$keys[0]] ?? null,
                                $row[$keys[1]] ?? null,
                                $row[$keys[2]] ?? null,
                                $row[$keys[3]] ?? null,
                                $row[$keys[4]] ?? false,
                                $row[$keys[5]] ?? null,
                                $row[$keys[6]] ?? null,
                                $row[$keys[7]] ?? null,
                                $row[$keys[8]] ?? null,
                                $row[$keys[9]] ?? null,
                                $row[$keys[10]] ?? null,
                            );
                            break;
                    }
                }

                $ps = json_encode($paymentMethodsObjects);

                echo "$ps\n\n";

                // Добавляем задержку
                sleep(1); // Задержка в 1 секунду
            } catch (\PDOException $e) {
                echo "[Error] Ошибка выполнения запроса для таблицы $tableName: " . $e->getMessage() . "\n";
            }
        }
    }
}
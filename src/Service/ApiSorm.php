<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSorm.php
 * Updated At: 18.10.2024, 15:59
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
                            $params = [];
                            foreach ($keys as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $subKey) {
                                        if (isset($row[$subKey]) && $row[$subKey] !== null && $row[$subKey] !== '') {
                                            $params[] = $row[$subKey];
                                        }
                                    }
                                } else {
                                    if (isset($row[$value]) && $row[$value] !== null && $row[$value] !== '') {
                                        $params[] = $row[$value];
                                    }
                                }
                            }
                            $logIsObjects[] = new LogIs(...$params); // Передаём параметры
                            break;

//                        case 'operations':
//                            $params = [];
//                            foreach ($keys as $key => $value) {
//                                if (is_array($value)) {
//                                    foreach ($value as $subKey) {
//                                        if (isset($row[$subKey]) && $row[$subKey] !== null && $row[$subKey] !== '') {
//                                            $params[] = $row[$subKey];
//                                        }
//                                    }
//                                } else {
//                                    if (isset($row[$value]) && $row[$value] !== null && $row[$value] !== '') {
//                                        $params[] = $row[$value];
//                                    }
//                                }
//                            }
//                            $operationsObjects[] = new Operation(...$params); // Передаём параметры
//                            break;
//
//                        case 'orders':
//                            $params = [];
//                            foreach ($keys as $key => $value) {
//                                if (is_array($value)) {
//                                    foreach ($value as $subKey) {
//                                        if (isset($row[$subKey]) && $row[$subKey] !== null && $row[$subKey] !== '') {
//                                            $params[] = $row[$subKey];
//                                        }
//                                    }
//                                } else {
//                                    if (isset($row[$value]) && $row[$value] !== null && $row[$value] !== '') {
//                                        $params[] = $row[$value];
//                                    }
//                                }
//                            }
//                            $ordersObjects[] = new Order(...$params); // Передаём параметры
//                            break;

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

//                        case 'person':
//                            $params = [];
//                            foreach ($keys as $key => $value) {
//                                if (is_array($value)) {
//                                    foreach ($value as $subKey) {
//                                        if (isset($row[$subKey]) && $row[$subKey] !== null && $row[$subKey] !== '') {
//                                            $params[] = $row[$subKey];
//                                        }
//                                    }
//                                } else {
//                                    if (isset($row[$value]) && $row[$value] !== null && $row[$value] !== '') {
//                                        $params[] = $row[$value];
//                                    }
//                                }
//                            }
//                            $personObjects[] = new Person(...$params); // Передаём параметры
//                            break;
//
//                        case 'tariffs':
//                            $params = [];
//                            foreach ($keys as $key => $value) {
//                                if (is_array($value)) {
//                                    foreach ($value as $subKey) {
//                                        if (isset($row[$subKey]) && $row[$subKey] !== null && $row[$subKey] !== '') {
//                                            $params[] = $row[$subKey];
//                                        }
//                                    }
//                                } else {
//                                    if (isset($row[$value]) && $row[$value] !== null && $row[$value] !== '') {
//                                        $params[] = $row[$value];
//                                    }
//                                }
//                            }
//                            $tariffObjects[] = new Tariff(...$params); // Передаём параметры
//                            break;
//
//                        case 'tickets':
//                            $params = [];
//                            foreach ($keys as $key => $value) {
//                                if (is_array($value)) {
//                                    foreach ($value as $subKey) {
//                                        if (isset($row[$subKey]) && $row[$subKey] !== null && $row[$subKey] !== '') {
//                                            $params[] = $row[$subKey];
//                                        }
//                                    }
//                                } else {
//                                    if (isset($row[$value]) && $row[$value] !== null && $row[$value] !== '') {
//                                        $params[] = $row[$value];
//                                    }
//                                }
//                            }
//                            $ticketsObjects[] = new Ticket(...$params); // Передаём параметры
//                            break;
                    }
                }
                $ps = json_encode($paymentMethodsObjects);
                echo $ps . PHP_EOL;

                // Добавляем задержку
                sleep(1); // Задержка в 1 секунду
            } catch (\PDOException $e) {
                echo "[Error] Ошибка выполнения запроса для таблицы $tableName: " . $e->getMessage() . "\n";
            }
        }
    }

}
<?php
/*
 * Copyright (c) 2024 - 2024, Webhost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ExportData.php
 * Updated At: 14.10.2024, 20:44
 */

namespace SormModule\Service;

use SormModule\Sorm;

final class ExportData
{
    private static $db;
    private static $settings;
    private static $batchSize;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        self::$settings = Sorm::loadSettings();
        self::$db = Sorm::initDatabase();
        if (!empty(self::$settings) && !empty(self::$db)) {
            self::$batchSize = 100;
            self::startExport(self::$batchSize);
        }
    }

    /**
     * Запускает экспорт данных по всем таблицам в базе данных.
     *
     * @throws \Exception
     */
    public static function startExport(int $batchSize)
    {
        // Получаем список всех таблиц
        $tables = self::getAllTables();

        // Проходим по каждой таблице
        foreach ($tables as $table) {
            $offset = 0;
            while (true) {
                // Получаем данные порциями по $batchSize
                $data = self::getDataFromTable($table, $batchSize, $offset);

                // Если данные закончились, выходим из цикла
                if (empty($data)) {
                    break;
                }

                // Отправляем данные в SORM API
                ApiSorm::exportToSorm($batchSize, $data);

                // Делаем паузу в 5–6 секунд
                sleep(rand(5, 6));

                // Увеличиваем смещение
                $offset += $batchSize;
            }
        }
    }

    /**
     * Получает список всех таблиц в базе данных.
     *
     * @return array
     */
    private static function getAllTables(): array
    {
        $query = self::$db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '" . self::$settings['database']['name'] . "' ORDER BY table_name ASC");
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Получает данные из таблицы порциями.
     *
     * @param string $table Название таблицы.
     * @param int $limit Количество записей для выборки.
     * @param int $offset Смещение.
     * @return array
     */
    private static function getDataFromTable(string $table, int $limit, int $offset): array
    {
        $query = self::$db->prepare("SELECT * FROM $table LIMIT :limit OFFSET :offset");
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
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
<?php
/*
 * Copyright (c) 2024 - 2024, Webhost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Sorm.php
 * Updated At: 14.10.2024, 21:10
 */

namespace SormModule;

use Exception;
use PDO;
use Symfony\Component\Yaml\Yaml;

final class Sorm
{
    private static $db;
    private static $settings;
    private static $path;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        self::findProjectRoot();
        self::loadSettings();
        self::initDatabase();
    }

    /**
     * Поиск и загрузка файла настроек settings.yaml
     *
     * @return mixed
     * @throws Exception
     */
    public static function loadSettings(): mixed
    {
        self::$path = Sorm::findProjectRoot();
        $settingsFilePath = self::$path . '/sorm/settings.yaml'; // Используем путь до корня проекта
        self::$settings = Yaml::parseFile($settingsFilePath);
        return self::$settings;
    }

    /**
     * Инициализация подключения к базе данных
     *
     * @return PDO|null
     * @throws Exception
     */
    public static function initDatabase(): ?PDO
    {
        self::loadSettings();
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8',
                self::$settings['database']['host'],
                self::$settings['database']['port']
            );
            self::$db = new PDO($dsn, self::$settings['database']['user'], self::$settings['database']['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            self::$db->exec('USE ' . self::$settings['database']['name']);
            self::log('Подключение к базе данных установлено.');
            return self::$db;
        } catch (Exception $e) {
            $data = json_encode(self::$db);
            $data2 = json_encode(self::$settings);
            $data3 = json_encode($dsn);
            $data4 = json_encode($e);
            throw new Exception("Error connecting to database: [{$data}] [{$data2}] [{$data3}] [{$data4}");
        }
    }

    /**
     * Экспорт данных в SORM API
     *
     * @return void
     */
    public static function exportToSorm(): void
    {
        $sormApiUrl = self::$settings['sormApiUrl'];
        $query = self::$db->query('SELECT * FROM some_table'); // Пример запроса

        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        self::sendDataToSorm($sormApiUrl, $data);
    }

    /**
     * Отправка данных в SORM API
     *
     * @param $url
     * @param $data
     * @return void
     */
    public static function sendDataToSorm($url, $data): void
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        self::log('Данные отправлены в SORM API.', ['response' => $response]);
    }

    /**
     * Запись логов в файл
     *
     * @param $message
     * @param array|null $context
     * @return void
     */
    public static function log($message, ?array $context = []): void
    {
        $date = date('d-m-Y');
        $env = self::$settings['env'];
        $logFile = self::$path . "/sorm/logs/{$env}-{$date}.log"; // Используем путь до корня проекта
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? json_encode($context) : '';

        $logMessage = sprintf("[%s] %s %s\n", $timestamp, $message, $contextString);
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Поиск корневой директории проекта
     * @throws Exception
     */
    public static function findProjectRoot(): ?string
    {
        $dir = getcwd();

        while ($dir !== '/' && !file_exists($dir . '/.env')) {
            $dir = dirname($dir);
        }

        // Сохраняем путь к корню проекта в свойстве path
        if (file_exists($dir . '/.env')) {
            self::$path = $dir;
            return self::$path;
        } else {
            throw new Exception('Project root (.env) not found.');
        }
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

<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Sorm.php
 * Updated At: 18.10.2024, 14:45
 *
 */

namespace SormModule;

use Exception;
use PDO;
use SormModule\Service\Security\ChipperSecurity;
use SormModule\Service\Security\SormService;
use Symfony\Component\Yaml\Yaml;

final class Sorm extends SormService
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
     * @throws Exception
     */
    public static function saveSettings(mixed $settings): void
    {
        self::$path = Sorm::findProjectRoot();
        $settingsFilePath = self::$path . '/sorm/settings.yaml';

        try {
            $yamlContent = Yaml::dump($settings);
            file_put_contents($settingsFilePath, $yamlContent);
            self::$settings = $settings;
        } catch (Exception $e) {
            throw new Exception("Ошибка при сохранении настроек: " . $e->getMessage());
        }
    }

    /**
     * Инициализация подключения к базе данных
     *
     * @param string|null $paymentMethod
     * @return PDO|null
     * @throws Exception
     */
    public static function initDatabase(?string $paymentMethod = ''): ?PDO
    {
        self::$settings = ChipperSecurity::decryptDb();
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
}

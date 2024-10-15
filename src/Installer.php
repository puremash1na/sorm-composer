<?php
/*
 * Copyright (c) 2024 - 2024, Webhost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Installer.php
 * Updated At: 14.10.2024, 21:01
 */

namespace SormModule;

use Symfony\Component\Yaml\Yaml;

final class Installer
{
    /**
     * Installs the SORM module by creating necessary directories and configuration files in the project root.
     *
     * This method:
     * - Searches for the .env file to determine the project root directory.
     * - Creates a logs directory for the SORM module if it doesn't already exist.
     * - Generates a settings.yaml file with default configuration if it does not exist.
     * - Copies the Sorm.php file to the project root.
     * - Logs success or failure messages to a log file within the logs directory.
     *
     * @return void
     */
    public static function install()
    {
        // Определяем корневую папку проекта по наличию файла .env
        $rootDir = self::findProjectRoot();
        if (!$rootDir) {
            echo "Project root (.env) not found.";
            return;
        }

        $logDir = $rootDir . '/sorm/logs';
        $settingsPath = $rootDir . '/sorm/settings.yaml';
        $executable = $rootDir . '/sorm/index.php';
        $srcSorm = file_get_contents(__DIR__ . '/index.php');

        // Текущая дата и время для логов
        $date = date('d-m-Y');
        $now = date('H:i:s');

        // Инициализация сообщения об ошибке
        $error = empty(error_get_last()) ? '' : error_get_last()['message'];

        // Создание папки для логов, если её не существует
        if (!is_dir($logDir)) {
            if (mkdir($logDir, 0777, true)) {
                // Логируем успешное создание директории
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Log directory created.\n", FILE_APPEND);
            } else {
                // Логируем ошибку, если директорию не удалось создать
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Failed to create directory: {$error}\n", FILE_APPEND);
            }
        }

        // Создание settings.yaml, если его не существует
        if (!file_exists($settingsPath)) {
            $defaultSettings = [
                'appName'    => 'SORM Module',
                'sormApiUrl' => 'http://example.com/api',
                'env'        => 'dev',
                'database'   => [
                    'host'     => 'localhost',
                    'name'     => 'dbname',
                    'user'     => 'dbuser',
                    'password' => 'dbpassword',
                    'port'     => '3306',
                ]
            ];
            // Попытка создать settings.yaml с дефолтными настройками
            if (file_put_contents($settingsPath, Yaml::dump($defaultSettings))) {
                file_put_contents($logDir . "/install-{$date}.log", "[$now] settings.yaml file created.\n", FILE_APPEND);
            } else {
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Failed to create settings.yaml file: $error\n", FILE_APPEND);
            }
        }

        // Копируем index.php, если его нет
        if (!file_exists($executable)) {
            if (file_put_contents($executable, $srcSorm)) {
                // Логируем успешное создание файла
                file_put_contents($logDir . "/install-{$date}.log", "[$now] index.php file created.\n", FILE_APPEND);
            } else {
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Failed to create index.php file: $error\n", FILE_APPEND);
            }
        }
    }

    /**
     * Находит корневую директорию проекта по наличию файла .env.
     * Рекурсивно поднимается по дереву директорий, начиная с текущей.
     *
     * @return string|null Путь к корневой директории проекта или null, если файл .env не найден.
     */
    private static function findProjectRoot(): ?string
    {
        $dir = getcwd();

        while ($dir !== '/' && !file_exists($dir . '/.env')) {
            $dir = dirname($dir);
        }

        return file_exists($dir . '/.env') ? $dir : null;
    }
}

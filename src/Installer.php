<?php

namespace SormModule; // Убедитесь, что это ваше пространство имен

use Symfony\Component\Yaml\Yaml;

class Installer
{
    public static function install()
    {
        $logDir = __DIR__ . '/../logs'; // Папка для логов
        $settingsPath = __DIR__ . '/../settings.yaml'; // Путь к файлу настроек

        // Создаем папку для логов, если она не существует
        if (!is_dir($logDir)) {
            if (mkdir($logDir, 0777, true)) {
                echo "Лог директория создана\n";
            } else {
                echo "Не удалось создать директорию: " . error_get_last()['message'] . "\n";
            }
        } else {
            echo "Лог директория уже существует\n";
        }

        // Создаем settings.yaml, если он не существует
        if (!file_exists($settingsPath)) {
            $defaultSettings = [
                'appName' => 'SORM Module',
                'sormApiUrl' => 'http://example.com/api',
                'database' => [
                    'host' => 'localhost',
                    'name' => 'dbname',
                    'user' => 'dbuser',
                    'password' => 'dbpassword',
                ],
            ];
            if (file_put_contents($settingsPath, Yaml::dump($defaultSettings))) {
                echo "Файл settings.yaml создан\n";
            } else {
                echo "Не удалось создать файл settings.yaml: " . error_get_last()['message'] . "\n";
            }
        } else {
            echo "Файл settings.yaml уже существует\n";
        }
    }
}

<?php

namespace Sorm;

use Symfony\Component\Yaml\Yaml;

class Installer
{
    public static function install()
    {
        $logDir = __DIR__ . '/../logs';
        $settingsPath = __DIR__ . '/../settings.yaml';

        // Создаем папку для логов, если она не существует
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
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
            file_put_contents($settingsPath, Yaml::dump($defaultSettings));
        }
    }
}

<?php

namespace Sorm;

class Installer
{
    public static function install()
    {
        if (!is_dir(__DIR__ . '/../logs')) {
            mkdir(__DIR__ . '/../logs', 0777, true);
        }

        // Создаем settings.yaml, если не существует
        $settingsPath = __DIR__ . '/../settings.yaml';
        if (!file_exists($settingsPath)) {
            $defaultSettings = [
                'appName' => 'SORM Module',
                'sormApiUrl' => 'http://example.com/api',
                'database' => [
                    'host' => 'localhost',
                    'name' => 'dbname',
                    'user' => 'dbuser',
                    'password' => 'dbpassword'
                ]
            ];
            file_put_contents($settingsPath, yaml_emit($defaultSettings));
        }
    }
}

<?php

require __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() === 'cli') {
    $argv = $_SERVER['argv'];

    if (isset($argv[1])) {
        $command = $argv[1];

        switch ($command) {
            case 'update':
                echo "Запуск первичной выгрузки на СОРМ\n";
                $settings = SormModule\Sorm::loadSettings();
                $settins = \json_encode($settings,true);
                echo "Параметры settings: $settins\n";
                break;
            case 'cron':
                echo "Запуск выгрузка данных на СОРМ, CRON";
                $settings = SormModule\Sorm::loadSettings();
                $settins = \json_encode($settings,true);
                echo "Параметры settings: $settins\n";
                break;
            case 'install':
                echo "Запуск миграции logs_edit и триггеров для отслеживания изменении в БД:";
                SormModule\Installer::installMigrations();
                SormModule\Installer::installTriggers();
            default:
                echo "Неизвестная команда: {$command}. Доступные команды: start, cron.\n";
                break;
        }
    } else {
        echo "Недостаточно аргументов. Формат: php index.php команда действие\n";
        echo "Доступные команды:\n";
        echo "start: Первичная выгрузка на СОРМ\n";
        echo "cron: Выгрузка данных на СОРМ, CRON\n";
    }
} else {
    echo "Этот скрипт можно запускать только из CLI.\n";
}

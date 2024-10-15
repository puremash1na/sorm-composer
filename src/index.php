<?php

require __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() === 'cli') {
    define("CLI_ALLOWED_COMMANDS", [
        "update"         => "Запуск первичной выгрузки на СОРМ",
        "cron"           => "Запуск выгрузка данных на СОРМ, CRON",
        "install"        => "Запуск миграции logs_edit и триггеров для отслеживания изменении в БД",
        "deleteTriggers" => "Удаление триггеров из базы данных",
    ]);
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
                SormModule\Installer::deleteTriggers();
                sleep(5);
                SormModule\Installer::installMigrations();
                SormModule\Installer::installTriggers();
                break;
            case 'deleteTriggers':
                echo "Удаление триггеров из базы данных:";
                SormModule\Installer::deleteTriggers();
                sleep(5);
            default:
                echo "Неизвестная команда: {$command}. Доступные команды: start, cron.\n";
                break;
        }
    } else {
        echo "Недостаточно аргументов. Формат: php sorm/index.php команда\n";
        echo "Доступные команды:\n";
        foreach (CLI_ALLOWED_COMMANDS as $desc => $command) {
            echo "php sorm/index.php $command: $desc\n";
        }
    }
} else {
    echo "Этот скрипт можно запускать только из CLI.\n";
}

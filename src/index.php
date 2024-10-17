<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: index.php
 * Updated At: 17.10.2024, 15:51
 *
 */


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
            case 'chipper':
                echo "Обновляем APP_KEY и шифруем данные\n";
                try {
                    \SormModule\Service\Security\ChipperSecurity::generateAppKey();
                    \SormModule\Service\Security\ChipperSecurity::encryptDb();
                    echo "Тестируем расшифровку данных БД:\n";
                    $data = SormModule\Service\Security\ChipperSecurity::decryptDb();
                    echo json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
                } catch (\Random\RandomException $e) {
                    continue;
                    echo "Произошла ошибка при выполнении команды chipper:" . $e->getMessage() . "\n";
                }
                break;
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
                break;
            default:
                echo "Неизвестная команда: {$command}. Формат: php sorm/index.php команда\n";
                echo "Доступные команды:\n";
                foreach (CLI_ALLOWED_COMMANDS as $command => $desc) {
                    echo "php sorm/index.php $command --- $desc\n";
                }
                break;
        }
    } else {
        echo "Недостаточно аргументов. Формат: php sorm/index.php команда\n";
        echo "Доступные команды:\n";
        foreach (CLI_ALLOWED_COMMANDS as $command => $desc) {
            echo "php sorm/index.php $command --- $desc\n";
        }
    }
} else {
    echo "Этот скрипт можно запускать только из CLI.\n";
}

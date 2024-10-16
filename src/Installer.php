<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Installer.php
 * Updated At: 16.10.2024, 13:29
 *
 */

namespace SormModule;

use Symfony\Component\Yaml\Yaml;

final class Installer
{
    public const PREFIX_INSERT_INTO_DESCRIPTION = [
        'logs_is'         => "Пользователь [#%userId%] перешел %urlPath%. Время: %datePrefix%",
        'operations'      => "Пользователь [#%userId%] совершил операцию [#%payId%], Name: %payName%, Время: %datePrefix%",
        'orders'          => "Пользователю [#%userId%] добавлена новая услуга [#%orderId%]. Время: %datePrefix%",
        'payment_methods' => "Администратор добавил новый платежный метод: %payName%, Отображание: %visible%, Время: %datePrefix%",
        'person'          => "Зарегестрирован новый пользователь [#%userId%], Время: %datePrefix%",
        'tariffs'         => "Администратор добавил новый тариф [#%tariffId%], Время: %datePrefix%",
        'tickets'         => "Пользователь [#%userId%] создал/ответил в запросах [#%ticketId%], Время: %datePrefix%",
    ];
    public const PREFIX_DELETE_DESCRIPTION = [
        'logs_is'         => "Удалена запись по переходу пользователя [#%userId%], path: %urlPath%. Время: %datePrefix%",
        'operations'      => "Удалена запись о совершении операции [#%payId%] пользователем [#%userId%], Name: %payName%, Время: %datePrefix%",
        'orders'          => "Удалена запись о добавлении новой услуги [#%orderId%] пользователю [#%userId%], Время: %datePrefix%",
        'payment_methods' => "Удалена запись о добавлении нового платежного метода [%payName%] администратором, Время: %datePrefix%",
        'person'          => "Удалена запись о регистрации нового пользователя [#%userId%], Время: %date%",
        'tariffs'         => "Удалена запись о добавлении нового тарифа [#%tariffId%] администратором, Время: %datePrefix%",
        'tickets'         => "Удалена запись о создании/ответа в запросах [#%ticketId%] пользователем [#%userId%], Время: %datePrefix%"
    ];
    /**
     * Ru: Устанавливает модуль SORM, создавая необходимые директории и файлы конфигурации в корне проекта.
     *
     * En: Installs the SORM module by creating necessary directories and configuration files in the project root.
     *
     * Ru: Этот метод:
     * - Выполняет поиск файла .env для определения корневого каталога проекта.
     * - Создает каталог журналов для модуля SORM, если он еще не существует.
     * - Создает settings.yaml с настройками по умолчанию, если он не существует.
     * - Копирует файл Sorm.php в корневой каталог проекта.
     * - Записывает сообщения об успешном завершении или сбое в файл журнала в каталоге logs.
     *
     * En: This method:
     * - Searches for the .env file to determine the project root directory.
     * - Creates a logs directory for the SORM module if it doesn't already exist.
     * - Generates a settings.yaml file with default configuration if it does not exist.
     * - Copies the Sorm.php file to the project root.
     * - Logs success or failure messages to a log file within the logs directory.
     *
     * @return void
     */
    public static function install(): void
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
                'appName'        => 'SORM Module',
                'sormApiUrl'     => 'http://example.com/api',
                'env'            => 'dev',
                'database'       => [
                    'driver'   => 'mysql',
                    'host'     => 'localhost',
                    'name'     => 'dbname',
                    'user'     => 'dbuser',
                    'password' => 'dbpassword',
                    'port'     => '3306',
                ],
                'paymentMethods' => [
                    'driver'   => 'mysql',
                    'host'     => 'localhost',
                    'name'     => 'dbname',
                    'user'     => 'dbuser',
                    'password' => 'dbpassword',
                    'port'     => '3306',
                ],
                'associationsDb' => [
                    // logs_is - БД СОРМ - logs - БД хостера
                    'logs_is'         => ['database'       => 'logs'],
                    'operations'      => ['database'       => 'pays'],
                    'orders'          => ['database'       => 'orders'],
                    'payment_methods' => ['paymentMethods' => 'payment_methods'],
                    'person'          => ['database'       => 'persons'],
                    'tariffs'         => ['database'       => 'tarifs'],
                    'tickets'         => ['database'       => 'tickets'],
                ],
                'associationsKeys' => [
                    // Выше указана связь между logs_is = logs по умолчанию
                    'logs_is'         => [
                        // поэтому данные ключи, связи нужно учиытвать с таблицей logs
                        'number'   => '',
                        'date'     => '',
                        'person'   => '',
                        'logged'   => '',
                        'ip'       => '',
                        'url'      => ''
                    ],
                    'operations'      => [
                        'number'       => '',
                        'person'       => '',
                        'type'         => '',
                        'order_number' => '',
                        'name'         => '',
                        'data'         => '',
                        'money'        => '',
                        'money_type'   => '',
                        'date'         => '',
                        'ip'           => '',
                        'money_date'   => '',
                        'money_key'    => '',
                        'money_status' => '',
                    ],
                    'orders'          => [
                        'number'  => '',
                        'date'    => '',
                        'expired' => '',
                        'from'    => '',
                        'type'    => '',
                        'server'  => '',
                        'name'    => '',
                        'vm_id'   => '',
                        'info'    => '',
                        'ip'      => '',
                        'tariff'  => '',
                        'price'   => '',
                        'closed'  => '',
                        'reason'  => '',
                        'test'    => '',
                    ],
                    'payment_methods' => [
                        'number'          => '',
                        'type'            => '',
                        'name'            => '',
                        'aggregator_name' => '',
                        'visible'         => '',
                    ],
                    'person'          => [
                        'login'          => '',
                        'email'          => '',
                        'reg_date'       => '',
                        'last_ip'        => '',
                        'last_login'     => '',
                        'last_access'    => '',
                        'contract'       => '',
                        'legal'          => '',
                        'name'           => '',
                        'birthday'       => '',
                        'company'        => '',
                        'inn'            => '',
                        'kpp'            => '',
                        'ogrn'           => '',
                        'legal_address'  => '',
                        'postal_address' => '',
                        'passport'       => '',
                        'telephone'      => '',
                        'fax'            => '',
                        'mobile'         => '',
                        'bank'           => '',
                        'verified'       => '',
                        'data'           => '',
                        'parent'         => '',
                    ],
                    'tariffs'         => [
                        'number'  => '',
                        'name'    => '',
                        'type'    => '',
                        'price'   => '',
                        'prolong' => '',
                        'periode' => '',
                    ],
                    'tickets'         => [
                        'number'     => '',
                        'parent'     => '',
                        'date'       => '',
                        'answered'   => '',
                        'ip'         => '',
                        'person'     => '',
                        'email'      => '',
                        'text'       => '',
                        'order_name' => '',
                        'closed'     => '',
                    ],
                ],
                'autoCronExport' => '01:00:00'
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
        } else {
            file_put_contents($executable, $srcSorm);
            file_put_contents($logDir . "/install-{$date}.log", "[$now] index.php file updated.\n", FILE_APPEND);
        }
    }

    /**
     * Ru: Находит корневую директорию проекта по наличию файла .env.
     * Рекурсивно поднимается по дереву директорий, начиная с текущей.
     *
     * En: Finds the root directory of the project by the presence of the .env file.
     * Recursively climbs the directory tree, starting from the current one.
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

    /**
     * Ru: Находит каталог журналов для модуля SORM
     *
     * En: Finds the log catalog for the SORM module
     * @return string|null
     */
    private static function getLogDir(): ?string
    {
        $rootDir = self::findProjectRoot();
        if (!$rootDir) {
            echo "Project root (.env) not found.";
            return null;
        }

        return '/sorm/logs';
    }
    /**
     * Ru: Запуск миграции logs_edit для отслеживаия изменений в базе данных
     *
     * En: Running logs_edit migration to track changes in the database
     *
     * @see Installer::install()
     * @throws \Exception
     */
    public static function installMigrations(): void
    {
        $database = Sorm::initDatabase();

        $sql = "
        CREATE TABLE IF NOT EXISTS logs_edit (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tableName VARCHAR(255) NOT NULL,
            recordId INT NOT NULL,
            action VARCHAR(50) NOT NULL,
            data JSON NOT NULL,
            comment TEXT NOT NULL,
            changedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
        $logDir = self::getLogDir();
        $date = date('d-m-Y');
        $now = date('H:i:s');
        try {
            $stmt = $database->prepare($sql);
            $stmt->execute();
            echo "[Migrations] Таблица logs_edit успешно создана.\n";
            file_put_contents($logDir . "/migrations-{$date}.log", "[$now] [Migrations] Таблица logs_edit успешно создана.\n", FILE_APPEND);
        } catch (\Exception $e) {
            echo "[Migrations] Ошибка при создании таблицы logs_edit: {$e->getMessage()}\n";
            file_put_contents($logDir . "/migrations-{$date}.log", "[$now] [Migrations] Ошибка при создании таблицы logs_edit: {$e->getMessage()}\n", FILE_APPEND);
        }
    }
    /**
     * Ru: Удаление триггеров в базе данных, у таблиц и полей которые должны отслеживаться
     *
     * En: Removing triggers in the database, tables and fields that should be monitored
     *
     * @see Installer::install()
     * @throws \Exception
     */
    public static function deleteTriggers(): void
    {
        $settings         = Sorm::loadSettings();
        $associationsDb   = $settings['associationsDb'];
        $associationsKeys = $settings['associationsKeys'];

        $logDir = self::getLogDir();
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $date = date('d-m-Y');
        $now = date('H:i:s');

        foreach ($associationsDb as $logicalTableName => $dbConfig) {
            $dbType = key($dbConfig);
            $tableName = $dbConfig[$dbType];

            // Получаем соответствующие креды для базы данных
            $dbCreds = $settings[$dbType] ?? null;
            if (!$dbCreds) {
                echo "[Error] Креды для базы данных {$dbType} не найдены.\n";
                continue;
            }

            // Инициализация соединения с базой данных
            $database = self::initDatabaseConnection($dbCreds);

            $keys = $associationsKeys[$logicalTableName] ?? null;
            if (!$keys) {
                continue;
            }

            $primaryKey = $keys['login'] ?? $keys['number'] ?? 'id';

            foreach ($keys as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey) {
                        if ($subKey === null || $subKey === '') {
                            continue;
                        }
                        $triggerName       = "before_{$tableName}_update_{$subKey}";
                        $triggerNameUpdate = "before_{$tableName}_update";

                        $triggerNameInsertSub = "before_{$tableName}_insert_{$subKey}";
                        $triggerNameInsert    = "before_{$tableName}_insert";

                        $triggerNameDeleteSub = "before_{$tableName}_delete_{$subKey}";
                        $triggerNameDelete    = "before_{$tableName}_delete";

                        // Удаляем существующий триггер, если он уже есть
                        $sqlDrop       = "DROP TRIGGER IF EXISTS {$triggerName}";
                        $sqlDropUpdate = "DROP TRIGGER IF EXISTS {$triggerNameUpdate}";

                        $sqlDropInsertSub = "DROP TRIGGER IF EXISTS {$triggerNameInsertSub}";
                        $sqlDropInsert    = "DROP TRIGGER IF EXISTS {$triggerNameInsert}";

                        $sqlDropDeleteSub = "DROP TRIGGER IF EXISTS {$triggerNameDeleteSub}";
                        $sqlDropDelete    = "DROP TRIGGER IF EXISTS {$triggerNameDelete}";

                        $database->exec($sqlDrop);
                        $database->exec($sqlDropUpdate);

                        $database->exec($sqlDropInsertSub);
                        $database->exec($sqlDropInsert);

                        $database->exec($sqlDropDeleteSub);
                        $database->exec($sqlDropDelete);

                        echo "[Migrations] Триггер для таблицы {$tableName}[$subKey]: успешно удален.\n";
                        file_put_contents($logDir . "/triggers-{$date}.log", "[$now] [Migrations] Триггер для таблицы {$tableName}[$subKey]: успешно удален.\n", FILE_APPEND);
                    }
                } else {
                    if($value === null || $value === '') {
                        continue;
                    }
                    $triggerName       = "before_{$tableName}_update_{$value}";
                    $triggerNameUpdate = "before_{$tableName}_update";

                    $triggerNameInsertSub = "before_{$tableName}_insert_{$value}";
                    $triggerNameInsert    = "before_{$tableName}_insert";

                    $triggerNameDeleteSub = "before_{$tableName}_delete_{$value}";
                    $triggerNameDelete    = "before_{$tableName}_delete";

                    // Удаляем существующий триггер, если он уже есть
                    $sqlDrop       = "DROP TRIGGER IF EXISTS {$triggerName}";
                    $sqlDropUpdate = "DROP TRIGGER IF EXISTS {$triggerNameUpdate}";

                    $sqlDropInsertSub = "DROP TRIGGER IF EXISTS {$triggerNameInsertSub}";
                    $sqlDropInsert    = "DROP TRIGGER IF EXISTS {$triggerNameInsert}";

                    $sqlDropDeleteSub = "DROP TRIGGER IF EXISTS {$triggerNameDeleteSub}";
                    $sqlDropDelete    = "DROP TRIGGER IF EXISTS {$triggerNameDelete}";

                    $database->exec($sqlDrop);
                    $database->exec($sqlDropUpdate);

                    $database->exec($sqlDropInsertSub);
                    $database->exec($sqlDropInsert);

                    $database->exec($sqlDropDeleteSub);
                    $database->exec($sqlDropDelete);
                    echo "[Migrations] Триггер для таблицы {$tableName}[$value]: успешно удален.\n";
                    file_put_contents($logDir . "/triggers-{$date}.log", "[$now] [Migrations] Триггер для таблицы {$tableName}[$value]: успешно удален.\n", FILE_APPEND);

                }
            }
        }
    }
    /**
     * Ru: Установка триггеров в базе данных, у таблиц и полей которые должны отслеживаться
     *
     * En: Setting triggers in the database, tables and fields that should be monitored
     *
     * @see Installer::install()
     * @throws \Exception
     */
    public static function installTriggers(): void {
        $settings = Sorm::loadSettings();
        $associationsDb = $settings['associationsDb'];
        $associationsKeys = $settings['associationsKeys'];
        $billing = $settings['database']['name'];
        $logDir = self::getLogDir();
        $date = date('d.m.Y');
        $now = date('H:i:s');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        foreach ($associationsDb as $logicalTableName => $dbConfig) {
            $dbType = key($dbConfig);
            $tableName = $dbConfig[$dbType];
            $dbCreds = $settings[$dbType] ?? null;

            if (!$dbCreds) {
                echo "[Error] Креды для базы данных {$dbType} не найдены.\n";
                continue;
            }

            $database = self::initDatabaseConnection($dbCreds);
            $keys = $associationsKeys[$logicalTableName] ?? null;

            if (!$keys) {
                continue;
            }

            $primaryKey = $keys['login'] ?? $keys['number'] ?? 'id';

            // Установка триггеров для каждого ключа
            foreach ($keys as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey) {
                        if($subKey === null || $subKey === '') {
                            continue;
                        }
                        self::installTriggerForKey($database, $billing, $tableName, $logicalTableName, $primaryKey,$keys, $subKey, $logDir, $date, $now);
                    }
                } else {
                    if($value === null || $value === '') {
                        continue;
                    }
                    self::installTriggerForKey($database, $billing, $tableName, $logicalTableName, $primaryKey, $keys, $value, $logDir, $date, $now);
                }
            }
        }
    }

    private static function installTriggerForKey(
        ?\PDO $database,
        string $billing,
        string $tableName,
        string $logicalTableName,
        string $primaryKey,
        array $fields,
        string $field,
        string $logDir,
        string $date,
        string $now
    ): void {

        $validFields = array_filter($fields, function($field) {
            return !is_null($field) && $field !== '';
        });

        // INSERT INTO триггер
        $insertTriggerName = "before_{$tableName}_insert";
        $insertLog = self::PREFIX_INSERT_INTO_DESCRIPTION[$logicalTableName] ?? '';
        self::createTrigger(
            $database, $billing, $insertTriggerName, $tableName, 'INSERT',$validFields,
            $field, $primaryKey, $insertLog, $logDir, $date, $now
        );

        // DELETE триггер
        $deleteTriggerName = "before_{$tableName}_delete";
        $deleteLog = self::PREFIX_DELETE_DESCRIPTION[$logicalTableName] ?? '';
        self::createTrigger(
            $database, $billing, $deleteTriggerName, $tableName, 'DELETE',$validFields,
            $field, $primaryKey, $deleteLog, $logDir, $date, $now
        );

        // UPDATE триггер (уже был реализован)
        $updateTriggerName = "before_{$tableName}_update_{$field}";
        $updateLog = "Изменение в таблице {$tableName} [trigger: {$updateTriggerName}]. Поле: {$field}. Время: {$now}";
        self::createTrigger(
            $database, $billing, $updateTriggerName, $tableName, 'UPDATE',$validFields,
            $field, $primaryKey, $updateLog, $logDir, $date, $now
        );
    }

    /**
     * @throws \Exception
     */
    private static function createTrigger(
        ?\PDO $database,
        string $billing,
        string $triggerName,
        string $tableName,
        string $operation,
        array $fields,
        string $fieldString,
        string $primaryKey,
        string $logTemplate,
        string $logDir,
        string $date,
        string $now
    ): void {
        // Удаление существующего триггера
        $sqlDrop = "DROP TRIGGER IF EXISTS {$triggerName}";
        $database->exec($sqlDrop);
        // Подготовка SQL для различных операций
        $sqlCreate = "";
        switch (strtoupper($operation)) {
            case 'INSERT':
                $jsonDataInfo = "JSON_OBJECT(" . implode(", ", array_map(fn($field) => "'{$field}', NEW.{$field}", $fields)) . ")";

                $sqlCreate = "
            CREATE TRIGGER {$triggerName} BEFORE INSERT ON {$tableName}
            FOR EACH ROW
            BEGIN
                DECLARE logMessage TEXT;
                SET logMessage = 'Inserted new Data to {$tableName} {$logTemplate}';

                INSERT INTO `{$billing}`.`logs_edit` (tableName, recordId, action, data, comment)
                VALUES (
                    '{$tableName}',
                    '0',
                    'INSERT',
                    {$jsonDataInfo},
                    logMessage
                );
            END;
            ";
                break;
            case 'DELETE':
                $jsonDataInfo = "JSON_OBJECT(" . implode(", ", array_map(fn($field) => "'{$field}', OLD.{$field}", $fields)) . ")";

                $sqlCreate = "
            CREATE TRIGGER {$triggerName} BEFORE DELETE ON {$tableName}
            FOR EACH ROW
            BEGIN
                DECLARE logMessage TEXT;
                SET logMessage = 'Deleted old data from {$tableName} {$logTemplate}';

                INSERT INTO `{$billing}`.`logs_edit` (tableName, recordId, action, data, comment)
                VALUES (
                    '{$tableName}',
                    '0',
                    'DELETE',
                    {$jsonDataInfo},
                    logMessage
                );
            END;
            ";
                break;
            case 'UPDATE':
                $sqlCreate = "
                    CREATE TRIGGER {$triggerName}
                    BEFORE UPDATE ON {$tableName}
                    FOR EACH ROW
                    BEGIN
                        DECLARE isOldSerialized BOOL;
                        DECLARE isNewSerialized BOOL;
                        DECLARE isOldJson BOOL;
                        DECLARE isNewJson BOOL;
                        
                        -- Проверяем, являются ли старые и новые данные сериализованными или JSON
                        SET isOldSerialized = (OLD.{$fieldString} IS NOT NULL AND LEFT(OLD.{$fieldString}, 2) = 'a:');
                        SET isNewSerialized = (NEW.{$fieldString} IS NOT NULL AND LEFT(NEW.{$fieldString}, 2) = 'a:');
                        SET isOldJson = (OLD.{$fieldString} IS NOT NULL AND LEFT(OLD.{$fieldString}, 1) = '{');
                        SET isNewJson = (NEW.{$fieldString} IS NOT NULL AND LEFT(NEW.{$fieldString}, 1) = '{');

                        -- Если оба значения сериализованы, сравниваем как массивы
                        IF isOldSerialized AND isNewSerialized THEN
                            IF (OLD.{$fieldString} != NEW.{$fieldString}) THEN
                                INSERT INTO `{$billing}`.`logs_edit` (tableName, recordId, action, data, comment)
                                VALUES (
                                    '{$tableName}', 
                                    OLD.id,
                                    'UPDATE', 
                                    JSON_OBJECT(
                                        'oldValue', OLD.{$fieldString},
                                        'newValue', NEW.{$fieldString}
                                    ), 
                                    CONCAT('Изменение в таблице {$tableName}. Поле: {$fieldString}. Время: ', NOW(), '. Предыдущее значение: ', OLD.{$fieldString})
                                );
                            END IF;

                        -- Если оба значения JSON, сравниваем как массивы
                        ELSEIF isOldJson AND isNewJson THEN
                            IF (JSON_UNQUOTE(JSON_EXTRACT(OLD.{$fieldString}, '$')) != JSON_UNQUOTE(JSON_EXTRACT(NEW.{$fieldString}, '$'))) THEN
                                INSERT INTO `{$billing}`.`logs_edit` (tableName, recordId, action, data, comment)
                                VALUES (
                                    '{$tableName}', 
                                    OLD.id,
                                    'UPDATE', 
                                    JSON_OBJECT(
                                        'oldValue', OLD.{$fieldString},
                                        'newValue', NEW.{$fieldString}
                                    ), 
                                    CONCAT('Изменение в таблице {$tableName}. Поле: {$fieldString}. Время: ', NOW(), '. Предыдущее значение: ', OLD.{$fieldString})
                                );
                            END IF;

                        -- Если не сериализованные и не JSON данные, проверяем как обычные строки
                        ELSEIF (OLD.{$fieldString} != NEW.{$fieldString}) AND (OLD.{$fieldString} IS NOT NULL AND OLD.{$fieldString} != '') AND (NEW.{$fieldString} IS NOT NULL AND NEW.{$fieldString} != '') THEN
                            INSERT INTO `{$billing}`.`logs_edit` (tableName, recordId, action, data, comment)
                            VALUES (
                                '{$tableName}', 
                                OLD.id,
                                'UPDATE', 
                                JSON_OBJECT(
                                    'oldValue', OLD.{$fieldString},
                                    'newValue', NEW.{$fieldString}
                                ), 
                                CONCAT('Изменение в таблице {$tableName}. Поле: {$fieldString}. Время: ', NOW(), '. Предыдущее значение: ', OLD.{$fieldString})
                            );
                        END IF;
                    END;
                    ";
                break;

            default:
                throw new \Exception("Неизвестная операция: {$operation}");
        }

        try {
            $stmt = $database->prepare($sqlCreate);
            $stmt->execute();
            echo "[Migrations] Триггер для таблицы {$tableName}[$fieldString][$operation]: успешно создан.\n";
            file_put_contents(
                "{$logDir}/triggers-{$date}.log",
                "[$now] [Migrations] Триггер для таблицы {$tableName}[$fieldString][$operation]: успешно создан.\n",
                FILE_APPEND
            );
        } catch (\Exception $e) {
            file_put_contents(
                "{$logDir}/triggers-{$date}.log",
                "[$now] [Migrations] Ошибка создания триггера для таблицы {$tableName}[$fieldString][$operation] [$triggerName] {$e->getMessage()}\n",
                FILE_APPEND
            );
            echo "[Migrations] Ошибка создания триггера для таблицы {$tableName}[$fieldString][$operation] [$triggerName] {$e->getMessage()}\n";
        }
    }

    private static function replaceMulti(string $template): string
    {

    }

    /**
     * Ru: Инициализация с базой данных исходя из ее данных авторизации
     *
     * En: Initialization with the database based on its authorization data
     *
     * @param array $dbCreds
     * @return \PDO|null
     */
    private static function initDatabaseConnection(array $dbCreds): ?\PDO
    {
        $dsn = "mysql:host={$dbCreds['host']};port={$dbCreds['port']};dbname={$dbCreds['name']}";
        try {
            return new \PDO($dsn, $dbCreds['user'], $dbCreds['password']);
        } catch (\PDOException $e) {
            echo "[Error] Не удалось подключиться к базе данных: " . $e->getMessage() . "\n";
            return null;
        }
    }
}

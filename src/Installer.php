<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Installer.php
 * Updated At: 14.10.2024, 13:43
 *
 */

namespace SormModule;

use Symfony\Component\Yaml\Yaml;

final class Installer
{
    /**
     * Installs the SORM module by creating necessary directories and configuration files.
     *
     * This method performs the following actions:
     * - Determines the current working directory and derives the root directory.
     * - Creates a logs directory for the SORM module if it doesn't already exist.
     * - Generates a settings.yaml file with default configuration if it does not exist.
     * - Logs success or failure messages to a log file within the logs directory.
     *
     * @return void
     */
    public static function install()
    {
        // Get the current working directory
        $currentDir = getcwd();

        // Derive the root directory by removing the '/public' suffix
        $rootDir = rtrim(dirname($currentDir), '/public');

        // Define the paths for the logs directory and the settings.yaml file
        $logDir       = $rootDir . '/sorm/logs';
        $settingsPath = $rootDir . '/sorm/settings.yaml';
        $executable   = $rootDir . '/sorm/Sorm.php';  // Новый файл Sorm.php
        $srcSorm      = file_get_contents(__DIR__ . '/Sorm.php');


        // Get the current date and time for logging
        $date = date('d-m-Y');
        $now  = date('H:i:s');

        // Initialize error message variable
        $error = empty(error_get_last()) ? '' : error_get_last()['message'];

        // Check if the logs directory exists; if not, attempt to create it
        if (!is_dir($logDir)) {
            if (mkdir($logDir, 0777, true)) {
                // Log success message if the directory was created
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Log directory created.\n", FILE_APPEND);
            } else {
                // Log error message if the directory creation failed
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Failed to create directory: {$error}\n", FILE_APPEND);
            }
        }

        // Check if the settings.yaml file exists; if not, create it with default settings
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
                ]
            ];
            // Attempt to write default settings to settings.yaml
            if (file_put_contents($settingsPath, Yaml::dump($defaultSettings))) {
                // Log success message if the file was created
                file_put_contents($logDir . "/install-{$date}.log", "[$now] settings.yaml file created.\n", FILE_APPEND);
            } else {
                // Log error message if the file creation failed
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Failed to create settings.yaml file: $error\n", FILE_APPEND);
            }
        }

        if (!file_exists($executable)) {
            if (file_put_contents($executable, $srcSorm)) {
                // Log success message if the file was created
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Sorm.php file created.\n", FILE_APPEND);
            } else {
                file_put_contents($logDir . "/install-{$date}.log", "[$now] Failed to create Sorm.php file: $error\n", FILE_APPEND);
            }
        }
    }
}

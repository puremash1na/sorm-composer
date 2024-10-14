<?php

namespace SormModule;

use PDO;
use Symfony\Component\Yaml\Yaml;

final class Sorm
{
    private $db;
    private $settings;
    private $path;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->findProjectRoot();
        $this->loadSettings();
        $this->initDatabase();
    }

    /**
     * Поиск и загрузка файла настроек settings.yaml
     *
     * @return void
     */
    private function loadSettings(): void
    {
        $settingsFilePath = $this->path . '/sorm/settings.yaml'; // Используем путь до корня проекта
        $this->settings = Yaml::parseFile($settingsFilePath);
    }

    /**
     * Инициализация подключения к базе данных
     *
     * @return void
     */
    private function initDatabase(): void
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8',
                $this->settings['database']['host'],
                $this->settings['database']['port']
            );
            $this->db = new PDO($dsn, $this->settings['database']['user'], $this->settings['database']['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $this->db->exec('USE ' . $this->settings['database']['name']);
            $this->log('Подключение к базе данных установлено.');
        } catch (\Exception $e) {
            $data = json_encode($this->db);
            $data2 = json_encode($this->settings);
            $data3 = json_encode($dsn);
            $data4 = json_encode($e);
            throw new \Exception("Error connecting to database: [{$data}] [{$data2}] [{$data3}] [{$data4}");
        }
    }

    /**
     * Экспорт данных в SORM API
     *
     * @return void
     */
    public function exportToSorm(): void
    {
        $sormApiUrl = $this->settings['sormApiUrl'];
        $query = $this->db->query('SELECT * FROM some_table'); // Пример запроса

        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $this->sendDataToSorm($sormApiUrl, $data);
    }

    /**
     * Отправка данных в SORM API
     *
     * @param $url
     * @param $data
     * @return void
     */
    private function sendDataToSorm($url, $data): void
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        $this->log('Данные отправлены в SORM API.', ['response' => $response]);
    }

    /**
     * Запись логов в файл
     *
     * @param $message
     * @param $context
     * @return void
     */
    private function log($message, $context = []): void
    {
        $date = date('d-m-Y');
        $logFile = $this->path . "/sorm/logs/{$this->settings['env']}-{$date}.log"; // Используем путь до корня проекта
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? json_encode($context) : '';

        $logMessage = sprintf("[%s] %s %s\n", $timestamp, $message, $contextString);
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Поиск корневой директории проекта
     * @throws \Exception
     */
    private function findProjectRoot(): void
    {
        $dir = getcwd();

        while ($dir !== '/' && !file_exists($dir . '/.env')) {
            $dir = dirname($dir);
        }

        // Сохраняем путь к корню проекта в свойстве path
        if (file_exists($dir . '/.env')) {
            $this->path = $dir;
        } else {
            throw new \Exception('Project root (.env) not found.');
        }
    }
}

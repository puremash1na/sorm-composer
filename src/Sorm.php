<?php

namespace SormModule;

use PDO;
use Symfony\Component\Yaml\Yaml;

final class Sorm
{
    private $db;
    private $settings;

    public function __construct()
    {
        $this->loadSettings();
        $this->initDatabase();
    }

    private function loadSettings()
    {
        $this->settings = Yaml::parseFile('settings.yaml');
    }

    // Инициализация подключения к базе данных
    private function initDatabase()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8',
            $this->settings['database']['host'],
            $this->settings['database']['name']
        );

        $this->db = new PDO($dsn, $this->settings['database']['user'], $this->settings['database']['password']);
        $this->log('Подключение к базе данных установлено.');
    }

    public function exportToSorm()
    {
        $sormApiUrl = $this->settings['sormApiUrl'];
        $query = $this->db->query('SELECT * FROM some_table'); // Пример запроса

        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $this->sendDataToSorm($sormApiUrl, $data);
    }

    private function sendDataToSorm($url, $data)
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

    // Запись логов в файл
    private function log($message, $context = [])
    {
        $date = date('d-m-Y');
        $logFile = "logs/{$this->settings['env']}-{$date}.log";
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? json_encode($context) : '';

        $logMessage = sprintf("[%s] %s %s\n", $timestamp, $message, $contextString);
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

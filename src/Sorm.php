<?php

namespace Sorm;

use PDO;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Yaml\Yaml;

final class Sorm
{
    private $db;
    private $logger;
    private $settings;

    public function __construct()
    {
        $this->loadSettings();
        $this->initLogger();
        $this->initDatabase();
    }

    private function loadSettings()
    {
        $this->settings = Yaml::parseFile(__DIR__ . '/../settings.yaml');
    }

    // Инициализация логгера
    private function initLogger()
    {
        $this->logger = new Logger('sorm');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/sorm.log', Logger::DEBUG));
    }
    private function initDatabase()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8',
            $this->settings['database']['host'],
            $this->settings['database']['name']
        );

        $this->db = new PDO($dsn, $this->settings['database']['user'], $this->settings['database']['password']);
        $this->logger->info('Подключение к базе данных установлено.');
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

        $this->logger->info('Данные отправлены в SORM API.', ['response' => $response]);
    }
}

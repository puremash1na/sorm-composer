<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSormService.php
 * Updated At: 18.10.2024, 14:45
 *
 */

namespace SormModule\Service\Api;

use Exception;
use SormModule\Service\Security\SormService;

final class ApiSormService extends SormService
{
    public function __construct() {}
    public static function queryApi() {}
    public static function downloadApi() {}
    public static function metricApi() {}
    public static function exportToSorm() {}
    /**
     * @throws Exception
     */
    public static function sendRequest(
        string $sormApuUrl,
        string $method,
        ?array $headers = null,
        ?array $body = null,
        ?array $params = null
    ): array {

        if (!empty($params)) {
            $queryString = http_build_query($params);
            $sormApuUrl .= '?' . $queryString;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $sormApuUrl);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        if (!empty($body)) {
            $jsonBody = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);

            if (!$headers) {
                $headers = [];
            }
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($jsonBody);
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('cURL Error: ' . $error);
        }

        curl_close($ch);

        return [
            'httpCode' => $httpCode,
            'response' => $response,
        ];
    }
}
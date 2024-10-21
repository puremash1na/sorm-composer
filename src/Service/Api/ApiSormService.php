<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: ApiSormService.php
 * Updated At: 21.10.2024, 16:51
 *
 */

namespace SormModule\Service\Api;

use Exception;
use SormModule\Service\Exception\ApiSormException;
use SormModule\Service\Security\SormService;

final class ApiSormService extends SormService
{
    public function __construct() {}
    public static function queryApi() {}
    public static function downloadApi() {}
    public static function metricApi() {}

    /**
     * @throws ApiSormException
     */
    public static function exportToSorm(string $sormApiUrl, string $sormKey, array $data)
    {
        try {
            echo "отправялем на $sormApiUrl/api/exportToSorm\n\n";
            $response = self::sendRequest(
                "$sormApiUrl/api/exportToSorm",
                'POST',
                null,
                $data,
                null,
                $sormKey
            );
        } catch (ApiSormException $e) {
            throw new ApiSormException($e->getCode(),$e->getMessage(),$e->getPrevious());
        }
    }
    /**
     * @throws Exception
     */
    public static function sendRequest(
        string $sormApuUrl,
        string $method,
        ?array $headers = null,
        ?array $body = null,
        ?array $params = null,
        ?string $sormKey = null,
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
            $headers[] = 'SORM-Key: ' . $sormKey;
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_encode($headers);

        echo $data.PHP_EOL;
        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        echo json_encode($response).PHP_EOL;
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            $error .="   //$httpCode";
            throw new ApiSormException(500, $error);
        }

        curl_close($ch);

        return [
            'httpCode' => $httpCode,
            'response' => $response,
        ];
    }
}
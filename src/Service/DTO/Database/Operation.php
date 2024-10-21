<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Operation.php
 * Updated At: 21.10.2024, 16:40
 *
 */

namespace SormModule\Service\DTO\Database;

use SormModule\Service\Security\SormService;

final class Operation
{
    public const TABLE_NAME = 'operations';
    public function __construct(
        private ?string $number,
        private ?string $person,
        private ?string $type,
        private ?string $orderNumber,
        private ?string $name,
        private ?string $data,
        private ?string $money,
        private ?string $moneyType,
        private ?string $date,
        private ?string $ip,
        private ?string $moneyDate,
        private ?string $moneyKey,
        private ?string $moneyStatus
    ) {

    }
    public function dataForExport(): array
    {
        return [
            'number'       => $this->number,
            'person'       => $this->person,
            'type'         => $this->type,
            'order_number' => $this->orderNumber,
            'name'         => $this->name,
            'data'         => $this->checkData($this->data),
            'money'        => $this->money,
            'money_type'   => $this->moneyType,
            'date'         => $this->date,
            'ip'           => $this->ip,
            'money_date'   => $this->moneyDate,
            'money_key'    => $this->moneyKey,
            'money_status' => $this->moneyStatus,
            'tableName'    => self::TABLE_NAME
        ];
    }
    private function checkData(mixed $data): string
    {
        if(mb_strlen($data) === 0) {
            return json_encode([]);
        }
        if (is_array($data)) {
            return json_encode($data);
        }
        if (@unserialize($data) !== false) {
            $data = @unserialize($data);
            return json_encode($data);
        }
        if (!is_string($data)) {
            return json_encode([]);
        }
        if (str_contains($data, "\n")) {
            $lines = explode("\n", trim($data));
            $result = [];

            foreach ($lines as $line) {
                if (str_contains($line, '=')) {
                    list($key, $value) = explode('=', $line, 2);
                    $result[trim($key)] = trim($value);
                }
            }

            return json_encode($result);
        } else {
            $returned = [];
            $returned['value'] = $data;
            return json_encode($returned);
        }
    }
}
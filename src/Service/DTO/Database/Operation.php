<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Operation.php
 * Updated At: 18.10.2024, 15:48
 *
 */

namespace SormModule\Service\DTO\Database;

final class Operation
{
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
            'data'         => $this->data,
            'money'        => $this->money,
            'money_type'   => $this->moneyType,
            'date'         => $this->date,
            'ip'           => $this->ip,
            'money_date'   => $this->moneyDate,
            'money_key'    => $this->moneyKey,
            'money_status' => $this->moneyStatus,
        ];
    }
}
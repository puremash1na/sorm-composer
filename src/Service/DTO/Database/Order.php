<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Order.php
 * Updated At: 18.10.2024, 15:48
 *
 */

namespace SormModule\Service\DTO\Database;

final class Order
{
    public function __construct(
        private ?string $number,
        private ?string  $date,
        private string  $expired,
        private ?string $from,
        private ?string $type,
        private ?string $server,
        private ?string $name,
        private ?string $vmId,
        private ?string $info,
        private ?string $ip,
        private ?string $tariff,
        private ?string $price,
        private ?string $closed,
        private ?string $reason,
        private ?bool    $test
    ) {

    }
    public function dataForExport(): array
    {
        return [
            'number'  => $this->number,
            'date'    => $this->date,
            'expired' => $this->expired,
            'from'    => $this->from,
            'type'    => $this->type,
            'server'  => $this->server,
            'name'    => $this->name,
            'vm_id'   => $this->vmId,
            'info'    => $this->info,
            'ip'      => $this->ip,
            'tariff'  => $this->tariff,
            'price'   => $this->price,
            'closed'  => $this->closed,
            'reason'  => $this->reason,
            'test'    => $this->test,
        ];
    }
}
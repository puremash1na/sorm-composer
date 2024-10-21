<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Order.php
 * Updated At: 21.10.2024, 16:07
 *
 */

namespace SormModule\Service\DTO\Database;

final class Order
{
    public const TABLE_NAME = 'orders';
    public function __construct(
        private ?string $number,
        private ?string $date,
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
        private ?bool   $test
    ) {

    }
    public function dataForExport(): array
    {
        return [
            'number'    => $this->number,
            'date'      => $this->date,
            'expired'   => $this->expired,
            'from'      => $this->from,
            'type'      => $this->type,
            'server'    => $this->server,
            'name'      => $this->name,
            'vm_id'     => $this->vmId,
            'info'      => $this->checkData($this->info),
            'ip'        => $this->ip,
            'tariff'    => $this->tariff,
            'price'     => $this->price,
            'closed'    => $this->closed,
            'reason'    => $this->reason,
            'test'      => $this->test,
            'tableName' => self::TABLE_NAME
        ];
    }
    private function checkData(mixed $data): string
    {
        if(is_array($data)) {
            return json_encode($data);
        }
        if(@unserialize($data) !== false) {
            $data = @unserialize($data);
            return json_encode($data);
        }
        if(!is_string($data)) {
            return json_encode([]);
        }
    }
}
<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Tariff.php
 * Updated At: 18.10.2024, 15:48
 *
 */

namespace SormModule\Service\DTO\Database;

final class Tariff
{
    public function __construct(
        private ?string $number,
        private ?string $name,
        private ?string $type,
        private ?string $price,
        private ?string $prolong,
        private ?string $periode
    ){

    }
    public function dataForExport(): array
    {
        return [
            'number'  => $this->number,
            'name'    => $this->name,
            'type'    => $this->type,
            'price'   => $this->price,
            'prolong' => $this->prolong,
            'periode' => $this->periode,
        ];
    }
}
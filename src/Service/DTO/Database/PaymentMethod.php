<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: PaymentMethod.php
 * Updated At: 18.10.2024, 15:48
 *
 */

namespace SormModule\Service\DTO\Database;

final class PaymentMethod
{
    public function __construct(
        private ?string $number,
        private ?string $type,
        private ?string $name,
        private ?string $aggregatorName,
        private ?bool   $visible
    ) {

    }
    public function dataForExport(): array
    {
        return [
            'number'          => $this->number,
            'type'            => $this->type,
            'name'            => $this->name,
            'aggregator_name' => $this->aggregatorName,
            'visible'         => $this->visible,
        ];
    }
}
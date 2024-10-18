<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: LogIs.php
 * Updated At: 18.10.2024, 15:24
 *
 */

namespace SormModule\Service\DTO\Database;

final class LogIs
{
    public function __construct(
        private ?string $id,
        private ?string $number,
        private ?string $date,
        private ?string $person,
        private ?bool    $logged,
        private ?string $ip,
        private ?string $url
    ){

    }
    public function dataForExport(): array
    {
        return [
            'id'     => $this->id,
            'number' => $this->number,
            'date'   => $this->date,
            'person' => $this->person,
            'logged' => $this->logged,
            'ip'     => $this->ip,
            'url'    => $this->url,
        ];
    }
}
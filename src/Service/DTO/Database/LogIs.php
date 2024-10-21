<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: LogIs.php
 * Updated At: 21.10.2024, 13:33
 *
 */

namespace SormModule\Service\DTO\Database;

final class LogIs
{
    public const TABLE_NAME = 'logs_is';
    public function __construct(
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
            'number'    => $this->number,
            'date'      => $this->date,
            'person'    => $this->person,
            'logged'    => $this->logged,
            'ip'        => $this->ip,
            'url'       => $this->url,
            'tableName' => self::TABLE_NAME
        ];
    }
}
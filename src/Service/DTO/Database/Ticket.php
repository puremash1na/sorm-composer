<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Ticket.php
 * Updated At: 21.10.2024, 13:33
 *
 */

namespace SormModule\Service\DTO\Database;

final class Ticket
{
    public const TABLE_NAME = 'tickets';
    public function __construct(
        private ?string $number,
        private ?string $parent,
        private ?string $date,
        private ?string $answered,
        private ?string $ip,
        private ?string $person,
        private ?string $email,
        private ?string $text,
        private ?string $orderName,
        private ?bool    $closed
    ) {

    }
    public function dataForExport(): array
    {
        return [
            'number'     => $this->number,
            'parent'     => $this->parent,
            'date'       => $this->date,
            'answered'   => $this->answered,
            'ip'         => $this->ip,
            'person'     => $this->person,
            'email'      => $this->email,
            'text'       => $this->text,
            'order_name' => $this->orderName,
            'closed'     => $this->closed,
            'tableName'  => self::TABLE_NAME
        ];
    }
}
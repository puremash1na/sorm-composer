<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Ticket.php
 * Updated At: 18.10.2024, 15:24
 *
 */

namespace SormModule\Service\DTO\Database;

final class Ticket
{
    public function __construct(
        private ?string $id,
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
            'id'         => $this->id,
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
        ];
    }
}
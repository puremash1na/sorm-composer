<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: Person.php
 * Updated At: 18.10.2024, 14:37
 *
 */

namespace SormModule\Service\DTO\Database;

final class Person
{
    public function __construct(
        private ?string $id,
        private ?string $login,
        private ?string $email,
        private ?string $regDate,
        private ?string $lastIp,
        private ?string $lastLogin,
        private ?string $lastAccess,
        private ?string $contract,
        private ?string $legal,
        private ?string $name,
        private ?string $birthday,
        private ?string $company,
        private ?string $inn,
        private ?string $kpp,
        private ?string $ogrn,
        private ?string $legalAddress,
        private ?string $postalAddress,
        private ?string $passport,
        private ?string $telephone,
        private ?string $fax,
        private ?string $mobile,
        private ?string $bank,
        private ?string $verified,
        private ?string $data,
        private ?string $parent
    ) {

    }
    public function dataForExport(): array
    {
        return [
            'id'             => $this->id,
            'login'          => $this->login,
            'email'          => $this->email,
            'reg_date'       => $this->regDate,
            'last_ip'        => $this->lastIp,
            'last_login'     => $this->lastLogin,
            'last_access'    => $this->lastAccess,
            'contract'       => $this->contract,
            'legal'          => $this->legal,
            'name'           => $this->name,
            'birthday'       => $this->birthday,
            'company'        => $this->company,
            'inn'            => $this->inn,
            'kpp'            => $this->kpp,
            'ogrn'           => $this->ogrn,
            'legal_address'  => $this->legalAddress,
            'postal_address' => $this->postalAddress,
            'passport'       => $this->passport,
            'telephone'      => $this->telephone,
            'fax'            => $this->fax,
            'mobile'         => $this->mobile,
            'bank'           => $this->bank,
            'verified'       => $this->verified,
            'data'           => $this->data,
            'parent'         => $this->parent,
        ];
    }
}
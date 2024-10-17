<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: SormService.php
 * Updated At: 17.10.2024, 13:31
 *
 */

namespace SormModule\Service\Security;

class SormService
{
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists(__CLASS__, $name)) {
            return forward_static_call_array([__CLASS__, $name], $arguments);
        }

        throw new \BadMethodCallException("Method {$name} does not exist");
    }
    public static function call(string $method, ?array $arguments = [])
    {
        return call_user_func_array([__CLASS__, $method], $arguments);
    }
}
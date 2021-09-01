<?php

namespace App\Tests;

trait UsersProvider
{
    public function usersIdsWithRoleUser()
    {
        return [
            [1],
            [2],
            [3]
        ];
    }

    public function usersIdsWithRoleAdmin()
    {
        return [];
    }
}

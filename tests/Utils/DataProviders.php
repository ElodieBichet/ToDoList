<?php

namespace App\Tests\Utils;

trait DataProviders
{
    public function usersAdminRoutes()
    {
        return [
            ['/users/create', 'Créer un utilisateur'],
            ['/users', 'Liste des utilisateurs'],
            ['/users/1/edit', 'Modifier'],
            ['/users/2/edit', 'Modifier']
        ];
    }

    public function usersProtectedRoutes()
    {
        return [
            ['/users'],
            ['/users/1/edit'],
            ['/users/2/edit'],
            ['/users/1/delete'],
            ['/users/2/delete']
        ];
    }

    public function tasksProtectedRoutes()
    {
        return [
            ['/tasks'],
            ['/tasks/create'],
            ['/tasks/1/edit'],
            ['/tasks/2/edit'],
            ['/tasks/1/toggle'],
            ['/tasks/2/toggle'],
            ['/tasks/1/delete'],
            ['/tasks/2/delete']
        ];
    }

    public function usersWithUserOrAdminRole()
    {
        return [
            ['user-1'],
            ['user-2'],
            ['user-admin']
        ];
    }

    public function usersWithUserRole()
    {
        return [
            ['user-1'],
            ['user-2'],
            ['user-3']
        ];
    }

    public function usersAndOneOfTheirTasks()
    {
        return [
            ['user-1', 'task-1'],
            ['user-2', 'task-2'],
            ['user-admin', 'task-admin']
        ];
    }

    public function invalidEmails()
    {
        return [
            ['email'],
            ['email@email'],
            ['@email.fr'],
            ['email email']
        ];
    }
}

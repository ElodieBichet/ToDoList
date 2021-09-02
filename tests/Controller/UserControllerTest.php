<?php

namespace App\Tests\Controller;

use App\Tests\LoginUser;
use App\Tests\UsersProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use LoginUser;
    use UsersProvider;

    /**
     * @dataProvider usersRoutes
     */
    public function testUsersPageResponse($uri, $h1Text)
    {
        $client = static::createClient();
        $client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $h1Text);
    }

    public function usersRoutes()
    {
        return [
            ['/users', 'Liste des utilisateurs'],
            ['/users/create', 'Créer un utilisateur'],
            ['/users/1/edit', 'Modifier'],
            ['/users/2/edit', 'Modifier']
        ];
    }
}

<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\LoginUser;
use App\Tests\UsersProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    use LoginUser;
    use UsersProvider;

    /**
     * @dataProvider usersIdsWithRoleUser
     */
    public function testIndex($userId)
    {
        $client = static::createClient();
        /** @var User */
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->find($userId);
        $this->login($client, $user);
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }
}

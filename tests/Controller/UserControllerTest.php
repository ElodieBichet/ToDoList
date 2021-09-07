<?php

namespace App\Tests\Controller;

use App\Tests\LoginUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserControllerTest extends WebTestCase
{
    use LoginUser;

    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    private $testClient = null;

    public function setUp(): void
    {
        $this->testClient = static::createClient();
        $this->databaseTool = $this->testClient->getContainer()->get(DatabaseToolCollection::class)->get();
    }

    /**
     * @dataProvider usersRoutes
     */
    public function testUsersPageResponse($uri, $h1Text)
    {
        $this->testClient->request('GET', $uri);

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

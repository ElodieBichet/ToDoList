<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\LoginUser;
use App\DataFixtures\UserFixtures;
use Symfony\Component\HttpFoundation\Response;
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

    public function testUsersPageResponse()
    {
        $this->testClient->request('GET', '/users/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'S\'enregistrer');
    }

    /**
     * @dataProvider usersProtectedRoutes
     */
    public function testLoginRedirectionIfNotAuthenticated($uri): void
    {
        $this->testClient->request('GET', $uri);
        $this->assertResponseRedirects(
            "http://localhost/login",
            Response::HTTP_FOUND,
            "No redirection to login page for uri : " . $uri
        );
    }

    public function usersProtectedRoutes()
    {
        return [
            ['/users'],
            ['/users/1/edit'],
            ['/users/2/edit']
        ];
    }

    /**
     * @dataProvider usersAdminRoutes
     */
    public function testUsersPageResponseIfAuthenticatedAsAdmin($uri, $h1Text)
    {
        /** @var User */
        $user = $this->databaseTool->loadFixtures([UserFixtures::class])->getReferenceRepository()->getReference('user-admin');

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', $uri);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $h1Text);
    }

    public function usersAdminRoutes()
    {
        return [
            ['/users/create', 'Créer un utilisateur'],
            ['/users', 'Liste des utilisateurs'],
            ['/users/1/edit', 'Modifier'],
            ['/users/2/edit', 'Modifier']
        ];
    }

    public function testForbiddenAccessForSimpleUser(): void
    {
        /** @var User */
        $user = $this->databaseTool->loadFixtures([UserFixtures::class])->getReferenceRepository()->getReference('user-1');

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->testClient->request('GET', '/users/1/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->testClient->request('GET', '/users/3/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}

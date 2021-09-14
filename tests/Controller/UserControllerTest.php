<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\LoginUser;
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
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-admin'];

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

    /**
     * @dataProvider usersWithUserRole
     */
    public function testForbiddenAccessForSimpleUser($userRef): void
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN, "A simple user should not have access to user list");

        $this->testClient->request('GET', '/users/' . ($user->getId() - 1) . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN, "A simple user should not be able to edit another user");

        $this->testClient->request('GET', '/users/' . ($user->getId() - 1) . '/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN, "A simple user should not be able to delete another user");
    }

    public function usersWithUserRole()
    {
        return [
            ['user-1'],
            ['user-2'],
            ['user-3']
        ];
    }

    public function testCreateUser(): void
    {
        $crawler = $this->testClient->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists("input#user_admin");

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'Username',
            'user[password][first]' => 'pa$$word',
            'user[password][second]' => 'pa$$word',
            'user[email]' => 'username@email.com'
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNotNull(
            self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'Username']),
            "The new created user is not found in database"
        );
    }

    public function testCreateUserWhenAuthenticatedAsAdmin(): void
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-admin'];

        $this->login($this->testClient, $user);

        $crawler = $this->testClient->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists("input#user_admin");

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'Username',
            'user[password][first]' => 'pa$$word',
            'user[password][second]' => 'pa$$word',
            'user[email]' => 'username@email.com',
            'user[admin]' => true
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        /** @var User */
        $user = self::$container->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'Username']);
        $this->assertNotNull($user, "The new created user is not found in database");
        $this->assertTrue(in_array("ROLE_ADMIN", $user->getRoles()), "Admin role has not been added as expected");
    }

    /**
     * @dataProvider usersWithUserRole
     */
    public function testEditUser($userRef)
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];

        $this->login($this->testClient, $user);

        $crawler = $this->testClient->request('GET', '/users/' . $user->getId() . '/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists("input#user_admin");

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'New username',
            'user[password][first]' => 'new-pa$$word',
            'user[password][second]' => 'new-pa$$word',
            'user[email]' => 'new-username@email.com'
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        /** @var User */
        $newuser = self::$container->get('doctrine')->getRepository(User::class)->find($user->getId());
        $this->assertSame("New username", $newuser->getUsername(), "The username has not been updated correctly.");
        $this->assertSame("new-username@email.com", $newuser->getEmail(), "The email has not been updated correctly.");
    }

    public function testEditUserWhenAuthenticatedAsAdmin()
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-admin'];

        $this->login($this->testClient, $user);

        $crawler = $this->testClient->request('GET', '/users/2/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists("input#user_admin");

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'New username',
            'user[password][first]' => 'new-pa$$word',
            'user[password][second]' => 'new-pa$$word',
            'user[email]' => 'new-username@email.com',
            'user[admin]' => true
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        /** @var User */
        $updateduser = self::$container->get('doctrine')->getRepository(User::class)->find(2);
        $this->assertSame("New username", $updateduser->getUsername(), "The username has not been updated correctly.");
        $this->assertSame("new-username@email.com", $updateduser->getEmail(), "The email has not been updated correctly.");
        $this->assertTrue(in_array("ROLE_ADMIN", $updateduser->getRoles()), "Admin role has not been added as expected");
    }

    public function testSuccessfulDeleteUserAsAdmin()
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-admin'];
        /** @var User */
        $userToDelete = $fixtures['user-1'];
        $id = $userToDelete->getId();

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/users/' . $id . '/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNull(
            self::$container->get('doctrine')->getRepository(User::class)->find($id),
            "The user has not been removed from DB as expected"
        );
    }
}

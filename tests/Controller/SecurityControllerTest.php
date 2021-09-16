<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Utils\LoginUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class SecurityControllerTest extends WebTestCase
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

    public function testDisplayLoginForm()
    {
        $this->testClient->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->testClient->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'fakeusername',
            '_password' => 'fakepassword'
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function usersWithUserOrAdminRole()
    {
        return [
            ['user-1'],
            ['user-2'],
            ['user-admin']
        ];
    }

    /**
     * @dataProvider usersWithUserOrAdminRole
     */
    public function testSuccessfullLogin($userRef)
    {
        $crawler = $this->testClient->request('GET', '/login');

        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => $user->getUsername(),
            '_password' => $user->getPassword()
        ]);
        $this->testClient->submit($form);

        // Check that user is authenticated
        $this->assertNotFalse(unserialize($this->testClient->getContainer()->get('session')->get('_security_main')));

        $this->assertResponseRedirects(
            "http://localhost/",
            Response::HTTP_FOUND
        );
        $this->testClient->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider usersWithUserOrAdminRole
     */
    public function testSuccessfullLogout($userRef)
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];
        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/logout');

        // Check that user is not authenticated
        $this->assertFalse(unserialize($this->testClient->getContainer()->get('session')->get('_security_main')));

        // Check if user is redirected to login page
        $this->testClient->followRedirect();
        $this->assertResponseRedirects(
            "http://localhost/login",
            Response::HTTP_FOUND
        );
    }
}

<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Utils\CustomFunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends CustomFunctionalTestCase
{
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

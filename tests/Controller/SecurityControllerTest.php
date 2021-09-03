<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\LoginUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use LoginUser;

    public function testDisplayLoginForm()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'fakeusername',
            '_password' => 'fakepassword'
        ]);
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSuccessfullLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'user-1',
            '_password' => 'mdp-user-1'
        ]);
        $client->submit($form);

        // Check that user is authenticated
        $this->assertNotFalse(unserialize($client->getContainer()->get('session')->get('_security_main')));

        $this->assertResponseRedirects(
            "http://localhost/",
            Response::HTTP_FOUND
        );
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testSuccessfullLogout()
    {
        $client = static::createClient();
        /** @var User */
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->find(1);
        $this->login($client, $user);

        $client->request('GET', '/logout');

        // Check that user is not authenticated
        $this->assertFalse(unserialize($client->getContainer()->get('session')->get('_security_main')));

        // Check if user is redirected to login page
        $client->followRedirect();
        $this->assertResponseRedirects(
            "http://localhost/login",
            Response::HTTP_FOUND
        );
    }
}

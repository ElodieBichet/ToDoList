<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    public function getNewClient(?bool $authenticated = false)
    {
        if ($authenticated) {
            return static::createClient([], [
                'HTTP_HOST'     => 'todolist-app',
                'PHP_AUTH_USER' => 'Elodie',
                'PHP_AUTH_PW'   => 'mdp-Elodie',
            ]);
        }
        return static::createClient([], ['HTTP_HOST' => 'todolist-app',]);
    }

    public function testTasksPageResponse(): void
    {
        $client = $this->getNewClient(true);
        $crawler = $client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        // $this->assertSelectorTextContains('h1', 'Hello World');
    }

    /**
     * @dataProvider tasksProtectedRoutes
     */
    public function testTasksLoginRedirection($uri): void
    {
        $client = $this->getNewClient();
        $client->request('GET', $uri);
        $this->assertResponseRedirects(
            "http://" . $client->getServerParameter('HTTP_HOST') . "/login",
            Response::HTTP_FOUND,
            "No redirection to login page for uri : " . $uri
        );
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
}

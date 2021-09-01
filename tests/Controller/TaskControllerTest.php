<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\LoginUser;
use App\Tests\UsersProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use LoginUser;
    use UsersProvider;

    /**
     * @dataProvider usersIdsWithRoleUser
     */
    public function testTasksPageResponse($userId): void
    {
        $client = static::createClient();
        /** @var User */
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->find($userId);

        $this->login($client, $user);

        $client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider tasksProtectedRoutes
     */
    public function testTasksLoginRedirection($uri): void
    {
        $client = static::createClient();
        $client->request('GET', $uri);
        $this->assertResponseRedirects(
            "http://localhost/login",
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

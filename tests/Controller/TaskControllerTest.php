<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\Utils\LoginUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class TaskControllerTest extends WebTestCase
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

    public function usersWithUserRole()
    {
        return [
            ['user-1'],
            ['user-2'],
            ['user-3']
        ];
    }

    public function usersAndOneOfTheirTasks()
    {
        return [
            ['user-1', 'task-1'],
            ['user-2', 'task-2'],
            ['user-admin', 'task-admin']
        ];
    }

    /**
     * @dataProvider usersWithUserRole
     */
    public function testTasksPageResponse($userRef): void
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider tasksProtectedRoutes
     */
    public function testLoginRedirectionIfNotAuthenticated($uri): void
    {
        $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);

        $this->testClient->request('GET', $uri);
        $this->assertResponseRedirects(
            "http://localhost/login",
            Response::HTTP_FOUND,
            "No redirection to login page for uri : " . $uri
        );
    }

    /**
     * @dataProvider usersWithUserRole
     */
    public function testCreateTaskWithConnectedUserAsAuthor($userRef)
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];
        $nbTasks = count(self::$container->get('doctrine')->getRepository(Task::class)->findBy(['author' => $user->getId()]));

        $this->login($this->testClient, $user);
        $crawler = $this->testClient->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Ma nouvelle tâche',
            'task[content]' => 'La description de ma nouvelle tâche'
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertEquals(
            $nbTasks + 1,
            count(self::$container->get('doctrine')->getRepository(Task::class)->findBy(['author' => $user->getId()])),
            "The number of tasks for the current user should be one more than before."
        );
    }

    public function testDeleteTaskForbiddenIfUserIsNotAuthor()
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-1'];
        /** @var Task */
        $task = $fixtures['task-2'];

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks/' . $task->getId() . '/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testEditTaskForbiddenIfUserIsNotAuthor()
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-1'];
        /** @var Task */
        $task = $fixtures['task-2'];

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks/' . $task->getId() . '/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testToggleTaskForbiddenIfUserIsNotAuthorOrAdmin()
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-1'];
        /** @var Task */
        $task = $fixtures['task-2'];

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks/' . $task->getId() . '/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider usersAndOneOfTheirTasks
     */
    public function testSuccessfulDeleteTaskAsAuthor($userRef, $taskRef)
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];
        /** @var Task */
        $task = $fixtures[$taskRef];
        $id = $task->getId();

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks/' . $id . '/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNull(
            self::$container->get('doctrine')->getRepository(Task::class)->find($id),
            "The task has not been removed from DB as expected"
        );
    }

    public function testSuccessfulDeleteAnonymousTaskAsAdmin()
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-admin'];
        /** @var Task */
        $task = $fixtures['task-anonymous'];
        $id = $task->getId();

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks/' . $task->getId() . '/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNull(
            self::$container->get('doctrine')->getRepository(Task::class)->find($id),
            "The task has not been removed from DB as expected"
        );
    }

    /**
     * @dataProvider usersAndOneOfTheirTasks
     */
    public function testSuccessfulEditTaskAsAuthor($userRef, $taskRef)
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];
        /** @var Task */
        $task = $fixtures[$taskRef];

        $this->login($this->testClient, $user);

        $crawler = $this->testClient->request('GET', '/tasks/' . $task->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Un titre modifié',
            'task[content]' => 'Un contenu modifié'
        ]);
        $this->testClient->submit($form);

        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        /** @var Task */
        $newTask = self::$container->get('doctrine')->getRepository(Task::class)->find($task->getId());
        $this->assertSame("Un titre modifié", $newTask->getTitle(), "The title has not been updated correctly.");
        $this->assertSame("Un contenu modifié", $newTask->getContent(), "The content has not been updated correctly.");
    }

    /**
     * @dataProvider usersAndOneOfTheirTasks
     */
    public function testSuccessfulToggleTaskAsAuthor($userRef, $taskRef)
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];
        /** @var Task */
        $task = $fixtures[$taskRef];
        $isDone = $task->isDone();

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNotEquals($isDone, $task->isDone());
    }

    public function testSuccessfulToggleTaskAsAdmin()
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures['user-admin'];
        /** @var Task */
        $task = $fixtures['task-1'];
        $isDone = $task->isDone();

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks/' . $task->getId() . '/toggle');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNotEquals($isDone, $task->isDone());
    }
}

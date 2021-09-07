<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\LoginUser;
use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
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

    public function testTasksPageResponse(): void
    {
        /** @var User */
        $user = $this->databaseTool->loadFixtures([UserFixtures::class])->getReferenceRepository()->getReference('user-1');

        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider tasksProtectedRoutes
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

    public function testCreateTaskWithConnectedUserAsAuthor()
    {
        /** @var User */
        $user = $this->databaseTool->loadFixtures([UserFixtures::class])->getReferenceRepository()->getReference('user-1');

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

        $this->assertCount(1, $user->getTasks(), "The number of tasks for the current user should be 1.");
    }

    public function testEditTask()
    {
        /** @var User */
        $user = $this->databaseTool->loadFixtures([UserFixtures::class, TaskFixtures::class])->getReferenceRepository()->getReference('user-1');

        $this->login($this->testClient, $user);
        $crawler = $this->testClient->request('GET', '/tasks/1/edit');
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
        $task = self::$container->get('doctrine')->getRepository(Task::class)->find(1);
        $this->assertSame("Un titre modifié", $task->getTitle(), "The title has not been updated correctly.");
        $this->assertSame("Un contenu modifié", $task->getContent(), "The content has not been updated correctly.");
    }

    public function testToggleTask()
    {
        /** @var User */
        $user = $this->databaseTool->loadFixtures([UserFixtures::class])->getReferenceRepository()->getReference('user-1');

        /** @var Task */
        $task = $this->databaseTool->loadFixtures([TaskFixtures::class])->getReferenceRepository()->getReference('task-1');
        $isDone = $task->isDone();

        $this->login($this->testClient, $user);
        $this->testClient->request('GET', '/tasks/1/toggle');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $this->assertNotEquals($isDone, $task->isDone());
    }

    public function testDeleteTask()
    {
        /** @var User */
        $user = $this->databaseTool->loadFixtures([UserFixtures::class, TaskFixtures::class])->getReferenceRepository()->getReference('user-1');
        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/tasks/1/delete');
        $this->assertResponseRedirects();
        $this->testClient->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        /** @var Task */
        $task = self::$container->get('doctrine')->getRepository(Task::class)->find(1);
        $this->assertNull($task);
    }
}

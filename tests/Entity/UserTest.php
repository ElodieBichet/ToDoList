<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\Utils\DataProviders;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use DataProviders;

    public function getUserEntity(): User
    {
        return (new User())
            ->setUsername('username')
            ->setEmail('user@email.com')
            ->setPassword('password');
    }

    public function assertHasErrors(User $user, int $number)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidUserEntity()
    {
        $this->assertHasErrors($this->getUserEntity(), 0);
    }

    public function testInvalidBlankUsername()
    {
        $this->assertHasErrors($this->getUserEntity()->setUsername(''), 1);
    }

    public function testInvalidBlankEmail()
    {
        $this->assertHasErrors($this->getUserEntity()->setEmail(''), 1);
    }

    /**
     * @dataProvider invalidEmails
     */
    public function testInvalidFormatEmail($email)
    {
        $this->assertHasErrors($this->getUserEntity()->setEmail($email), 1);
    }

    public function testDefaultUserRole()
    {
        $this->assertSame(['ROLE_USER'], $this->getUserEntity()->getRoles());
    }

    public function testAddTasksToUser()
    {
        $task1 = new Task;
        $task2 = new Task;
        $user = $this->getUserEntity()->addTask($task1);
        $this->assertSame($task1->getAuthor(), $user);
        $this->assertCount(1, $user->getTasks());
        $this->assertCount(2, $user->addTask($task2)->getTasks());
    }

    public function testRemoveTasksFromUser()
    {
        $task1 = new Task;
        $task2 = new Task;
        $user = $this->getUserEntity()->addTask($task1)->addTask($task2);
        $this->assertCount(1, $user->removeTask($task1)->getTasks());
        $this->assertCount(0, $user->removeTask($task2)->getTasks());
        $this->assertSame(null, $task2->getAuthor());
    }
}

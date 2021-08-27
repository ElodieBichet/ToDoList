<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    public function getTaskEntity(): Task
    {
        return (new Task())
            ->setTitle('The title')
            ->setContent('The task description')
            ->setCreatedAt('2021-08-27 15:50')
            ->toggle(0);
    }

    public function assertHasErrors(Task $task, int $number)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($task);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidTaskEntity()
    {
        $this->assertHasErrors($this->getTaskEntity(), 0);
    }

    public function testInvalidBlankTitle()
    {
        $this->assertHasErrors($this->getTaskEntity()->setTitle(''), 1);
    }

    public function testInvalidBlankContent()
    {
        $this->assertHasErrors($this->getTaskEntity()->setContent(''), 1);
    }

    public function testDefaultIsDoneIsFalse()
    {
        $this->assertEquals(false, $this->getTaskEntity()->isDone());
    }
}

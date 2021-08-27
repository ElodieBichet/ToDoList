<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
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

    public function invalidEmails()
    {
        return [
            ['email'],
            ['email@email'],
            ['@email.fr'],
            ['email email']
        ];
    }
}

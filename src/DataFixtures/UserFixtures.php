<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $usernames = ['Elodie', 'Adrien', 'Mehdi'];

        foreach ($usernames as $username) {
            $user = (new User());
            $hash = $this->encoder->encodePassword($user, "mdp-" . $username);
            $user->setUsername($username)
                ->setPassword($hash)
                ->setEmail($username . '@email.com');
            $manager->persist($user);
        }

        $manager->flush();
    }
}

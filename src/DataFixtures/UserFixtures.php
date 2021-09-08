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
        $admin = (new User)
            ->setUsername("user-admin")
            ->setEmail("user-admin@email.com")
            ->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->encoder->encodePassword($admin, "mdp-user-admin"));
        $manager->persist($admin);
        $this->setReference("user-admin", $admin);

        for ($i = 1; $i <= 7; $i++) {
            $user = (new User());
            $hash = $this->encoder->encodePassword($user, "mdp-user-$i");
            $user->setUsername("user-$i")
                ->setPassword($hash)
                ->setEmail($user->getUsername() . '@email.com');
            $manager->persist($user);
            $this->setReference("user-$i", $user);
        }

        $manager->flush();
    }
}

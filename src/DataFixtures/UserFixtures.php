<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $demoUser = (new User());
        $demoUser->setUsername("DemoUser")
            ->setPassword($this->encoder->encodePassword($demoUser, "DemoUserMDP"))
            ->setEmail('demouser@email.com');
        $manager->persist($demoUser);

        $demoAdmin = (new User());
        $demoAdmin->setUsername("DemoAdmin")
            ->setPassword($this->encoder->encodePassword($demoAdmin, "DemoAdminMDP"))
            ->setEmail('demoadmin@email.com')
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($demoAdmin);

        for ($i = 1; $i <= 12; $i++) {
            $user = (new User());
            $hash = $this->encoder->encodePassword($user, $faker->password());
            $user->setUsername($faker->userName())
                ->setPassword($hash)
                ->setEmail($user->getUsername() . '@email.com');
            if (mt_rand(1, 4) == 1) {
                $user->setRoles(["ROLE_ADMIN"]);
            }
            $manager->persist($user);
        }

        $manager->flush();
    }
}

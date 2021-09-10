<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use App\DataFixtures\UserFixtures;
use Psr\Container\ContainerInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $users = $this->container->get('doctrine')->getRepository(User::class)->findAll();

        for ($i = 1; $i <= 30; $i++) {
            $author = (mt_rand(1, 10) < 8) ? $users[$faker->randomDigit(count($users) - 1)] : null;
            $task = (new Task())
                ->setTitle(ucfirst($faker->words(3, true)))
                ->setContent($faker->paragraph(2))
                ->setAuthor($author)
                ->setCreatedAt($faker->dateTimeBetween('-50 days'))
                ->toggle($faker->boolean(20));
            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}

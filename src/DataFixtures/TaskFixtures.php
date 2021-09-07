<?php

namespace App\DataFixtures;

use DateTime;
use DateInterval;
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
        $users = $this->container->get('doctrine')->getRepository(User::class)->findAll();

        for ($i = 1; $i <= 20; $i++) {
            $author = (mt_rand(1, 10) < 8) ? $users[mt_rand(0, count($users) - 1)] : null;
            $task = (new Task())
                ->setTitle("Ma tâche $i")
                ->setContent("Le contenu de ma tâche $i")
                ->setAuthor($author)
                ->setCreatedAt(
                    (new DateTime())->sub(new DateInterval('P' . rand(0, 7) . 'D'))
                )
                ->toggle(mt_rand(0, 1));
            $manager->persist($task);
            $this->setReference("task-$i", $task);
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

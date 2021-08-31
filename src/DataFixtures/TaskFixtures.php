<?php

namespace App\DataFixtures;

use App\Entity\Task;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $task = (new Task())
                ->setTitle("Ma tâche $i")
                ->setContent("Le contenu de ma tâche $i")
                ->setCreatedAt(
                    (new DateTime())->sub(new DateInterval('P' . rand(0, 7) . 'D'))
                );
            $manager->persist($task);
        }

        $manager->flush();
    }
}

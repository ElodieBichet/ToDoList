<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Utils\CustomFunctionalTestCase;

class DefaultControllerTest extends CustomFunctionalTestCase
{
    /**
     * @dataProvider usersWithUserOrAdminRole
     */
    public function testIndex($userRef)
    {
        $fixtures = $this->databaseTool->loadAliceFixture([__DIR__ . '/../DataFixtures/UserTaskFixturesTest.yaml'], false);
        /** @var User */
        $user = $fixtures[$userRef];
        $this->login($this->testClient, $user);

        $this->testClient->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }
}

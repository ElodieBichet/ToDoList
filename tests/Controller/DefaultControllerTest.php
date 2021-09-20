<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Utils\LoginUser;
use App\Tests\Utils\DataProviders;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class DefaultControllerTest extends WebTestCase
{
    use LoginUser;
    use DataProviders;

    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    private $testClient = null;

    public function setUp(): void
    {
        $this->testClient = static::createClient();
        $this->databaseTool = $this->testClient->getContainer()->get(DatabaseToolCollection::class)->get();
    }

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

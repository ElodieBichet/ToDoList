<?php

namespace App\Tests\Utils;

use App\Tests\Utils\LoginUser;
use App\Tests\Utils\DataProviders;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

abstract class CustomFunctionalTestCase extends WebTestCase
{
    use LoginUser;
    use DataProviders;

    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    protected $testClient = null;

    public function setUp(): void
    {
        $this->testClient = static::createClient();
        $this->databaseTool = $this->testClient->getContainer()->get(DatabaseToolCollection::class)->get();
    }
}

<?php

namespace App\Tests;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceTestCase extends WebTestCase
{
    protected ContainerInterface $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->container = static::createClient()->getContainer();
    }
}

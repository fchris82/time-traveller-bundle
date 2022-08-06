<?php

namespace Fchris82\TimeTravellerBundle\Tests;

use Fchris82\TimeTravellerBundle\Manager\TimeManager;
use Fchris82\TimeTravellerBundle\TimeTravellerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class FunctionalTest extends TestCase
{
    public function testServiceWiring()
    {
        $kernel = new TestKernel([
            'time_passing' => false,
        ]);
        $file = new Filesystem();
        $file->remove($kernel->getCacheDir());

        $kernel->boot();
        $container = $kernel->getContainer();

        $manager = $container->get('fchris82.time_manager');
        self::assertInstanceOf(TimeManager::class, $manager);

        $manager->setNow(new \DateTime('2010-01-01'));
        self::assertTrue($manager->isShifted());
    }
}

class TestKernel extends Kernel
{
    private $timeTravellerConfig;

    public function __construct(array $timeTravellerConfig = [])
    {
        $this->timeTravellerConfig = $timeTravellerConfig;
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return [
            new TimeTravellerBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function(ContainerBuilder $container) {
            $container->loadFromExtension('time_traveller', $this->timeTravellerConfig);
        });
    }
}

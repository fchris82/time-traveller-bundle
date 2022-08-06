<?php

namespace Fchris82\TimeTravellerBundle\Tests\Manager;

use Fchris82\TimeTravellerBundle\Manager\TimeManager;
use PHPUnit\Framework\TestCase;

class TimeManagerTest extends TestCase
{
    /**
     * @param callable $modify
     * @param string   $expectedDate
     *
     * @return void
     *
     * @dataProvider dpTestModify
     */
    public function testModify(callable $modify, string $expectedDate)
    {
        $manager = new TimeManager(false);
        $realNow = $manager->getNow();
        $realTimestamp = $realNow->getTimestamp();
        self::assertFalse($manager->isShifted());
        $manager->setNow(new \DateTime('2010-01-01'));
        self::assertTrue($manager->isShifted());
        $current = $manager->getNow();
        $modify($manager);
        $shifted = $manager->getNow();
        self::assertEquals($realTimestamp, $realNow->getTimestamp(), 'Real date should not have changed.');
        self::assertEquals('2010-01-01', $current->format('Y-m-d'));
        self::assertEquals($expectedDate, $shifted->format('Y-m-d'));
    }

    public function dpTestModify()
    {
        return [
            'Change with setNow()' => [
                function (TimeManager $manager) { $manager->setNow(new \DateTime('2010-01-07')); },
                '2010-01-07',
            ],
            'Change with modify()' => [
                function (TimeManager $manager) { $manager->modify('+1 week'); },
                '2010-01-08',
            ],
            'Change with shiftForward()' => [
                function (TimeManager $manager) { $manager->shiftForward(new \DateInterval('P3D')); },
                '2010-01-04',
            ],
            'Change with shiftbackward()' => [
                function (TimeManager $manager) { $manager->shiftBackward(new \DateInterval('P3D')); },
                '2009-12-29',
            ],
        ];
    }

    public function testTimeGoesOn()
    {
        $manager = new TimeManager(true);
        self::assertFalse($manager->isShifted());
        $manager->setNow(new \DateTime('2010-01-01 12:00:00'));
        sleep(1);
        $this->assertNotEquals('2010-01-01 12:00:00', $manager->getSqlNow());
    }

    public function testTimeDoesNotGoOn()
    {
        $manager = new TimeManager(false);
        self::assertFalse($manager->isShifted());
        $manager->setNow(new \DateTime('2010-01-01 12:00:00'));
        sleep(1);
        $this->assertEquals('2010-01-01 12:00:00', $manager->getSqlNow());
    }
}

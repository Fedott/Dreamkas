<?php

namespace Lighthouse\CoreBundle\Tests\Command\Product;

use Lighthouse\CoreBundle\Command\Products\RecalculateMetricsCommand;
use Lighthouse\CoreBundle\Document\Product\Store\StoreProductMetricsCalculator;
use Lighthouse\CoreBundle\Test\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RecalculateMetricsCommandTest extends TestCase
{
    public function testExecute()
    {
        /* @var StoreProductMetricsCalculator|\PHPUnit_Framework_MockObject_MockObject $mock */
        $mock = $this
            ->getMockBuilder('Lighthouse\\CoreBundle\\Document\\Product\\Store\\StoreProductMetricsCalculator')
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('recalculateAveragePrice');
        $mock
            ->expects($this->once())
            ->method('recalculateDailyAverageSales');

        $command = new RecalculateMetricsCommand($mock);

        $commandTester = new CommandTester($command);

        $input = array();

        $exitCode = $commandTester->execute($input);

        $this->assertEquals(0, $exitCode);

        $this->assertContains('Recalculate started', $commandTester->getDisplay());
        $this->assertContains('Recalculate finished', $commandTester->getDisplay());
    }
}

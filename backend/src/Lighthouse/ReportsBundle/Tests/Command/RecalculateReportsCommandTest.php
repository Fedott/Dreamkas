<?php

namespace Lighthouse\ReportsBundle\Tests\Command;

use Lighthouse\ReportsBundle\Command\RecalculateReportsCommand;
use Lighthouse\ReportsBundle\Reports\GrossMargin\GrossMarginManager;
use Lighthouse\ReportsBundle\Reports\GrossMarginSales\GrossMarginSalesReportManager;
use Lighthouse\ReportsBundle\Reports\GrossSales\GrossSalesReportManager;
use Lighthouse\CoreBundle\Test\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RecalculateReportsCommandTest extends TestCase
{
    public function testExecute()
    {
        /* @var GrossSalesReportManager|\PHPUnit_Framework_MockObject_MockObject $grossSalesManagerMock */
        $grossSalesManagerMock = $this
            ->getMockBuilder('Lighthouse\\ReportsBundle\\Reports\\GrossSales\\GrossSalesReportManager')
            ->disableOriginalConstructor()
            ->getMock();

        $grossSalesManagerMock
            ->expects($this->once())
            ->method('recalculateStoreGrossSalesReport');
        $grossSalesManagerMock
            ->expects($this->once())
            ->method('recalculateGrossSalesProductReport');

        /* @var GrossMarginManager|\PHPUnit_Framework_MockObject_MockObject $grossMarginManagerMock */
        $grossMarginManagerMock = $this
            ->getMockBuilder('Lighthouse\\ReportsBundle\\Reports\\GrossMargin\\GrossMarginManager')
            ->disableOriginalConstructor()
            ->getMock();

        $grossMarginManagerMock
            ->expects($this->exactly(3))
            ->method($this->anything());

        /* @var GrossMarginSalesReportManager|\PHPUnit_Framework_MockObject_MockObject $grossMarginManagerMock */
        $grossMarginSalesReportManagerMock = $this
            ->getMockBuilder('Lighthouse\\ReportsBundle\\Reports\\GrossMarginSales\\GrossMarginSalesReportManager')
            ->disableOriginalConstructor()
            ->getMock();

        $grossMarginSalesReportManagerMock
            ->expects($this->exactly(1))
            ->method($this->anything());

        $command = new RecalculateReportsCommand(
            $grossSalesManagerMock,
            $grossMarginManagerMock,
            $grossMarginSalesReportManagerMock
        );

        $commandTester = new CommandTester($command);

        $input = array();

        $exitCode = $commandTester->execute($input);

        $this->assertEquals(0, $exitCode);

        $this->assertContains('Recalculate reports started', $commandTester->getDisplay());
        $this->assertContains('Recalculate reports finished', $commandTester->getDisplay());
    }
}

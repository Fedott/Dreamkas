<?php

namespace Lighthouse\ReportsBundle\Command;

use Lighthouse\CoreBundle\Document\Project\Project;
use Lighthouse\CoreBundle\Security\Project\ProjectContext;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service("lighthouse.reports.command.recalculate")
 * @DI\Tag("console.command")
 */
class RecalculateReportsCommand extends Command
{
    /**
     * @var ProjectContext
     */
    protected $projectContext;

    /**
     * @DI\InjectParams({
     *      "projectContext" = @DI\Inject("project.context")
     * })
     * @param ProjectContext $projectContext
     */
    public function __construct(ProjectContext $projectContext)
    {
        parent::__construct('lighthouse:reports:recalculate');

        $this->projectContext = $projectContext;
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('Recalculate Reports Data');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Recalculate reports started</info>");

        $this->projectContext->applyInProjects(
            function (Project $project, ContainerInterface $container) use ($output) {
                $grossMarginManager = $container->get('lighthouse.reports.gross_margin.manager');
                $grossMarginSalesReportManager = $container->get('lighthouse.reports.gross_margin_sales.manager');
                $costOfInventoryManager = $container->get('lighthouse.reports.cost_of_inventory.manager');

                $grossReturnReportManager = $container
                    ->get('lighthouse.reports.document.gross_return.manager');
                try {
                    $output->writeln("<info>Recalculate reports for project {$project->getName()}");

                    $output->writeln("<info>Cost Of Goods</info>");
                    $grossMarginManager->calculateGrossMarginUnprocessedTrialBalance($output);

                    $output->writeln("<info>Gross Margin Sales</info>");
                    $output->writeln("<info>Products</info>");
                    $grossMarginSalesReportManager->recalculateProductReport($output);

                    $output->writeln("<info>Catalog Groups</info>");
                    $grossMarginSalesReportManager->recalculateCatalogGroupReport($output);

                    $output->writeln("<info>Stores</info>");
                    $grossMarginSalesReportManager->recalculateStoreReport($output);

                    $output->writeln("<info>Network</info>");
                    $grossMarginSalesReportManager->recalculateNetworkReport($output);

                    $output->writeln("<info>Cost of inventory</info>");
                    $output->writeln("<info>Stores</info>");
                    $costOfInventoryManager->recalculateStoreCostOfInventory($output);


                    $output->writeln("<info>Gross Return</info>");
                    $grossReturnReportManager->recalculateNetworkReport($output);
                } catch (\Exception $e) {
                    $output->writeln(sprintf("<error>%s</error>", (string) $e));
                }
            }
        );

        $output->writeln("<info>Recalculate reports finished</info>");

        return 0;
    }
}

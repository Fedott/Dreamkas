<?php

namespace Lighthouse\CoreBundle\Tests\Command\Import;

use Lighthouse\CoreBundle\Integration\Set10\Import\Set10Import;
use Lighthouse\CoreBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class Set10SalesImportTest extends WebTestCase
{
    public function testExecute()
    {
        $this->createStore('197');
        $this->createStore('666');
        $this->createStore('777');
        $this->createProductsBySku(
            array(
                '1',
                '3',
                '7',
                '8594403916157',
                '2873168',
                '2809727',
                '25525687',
                '55557',
                '8594403110111',
                '4601501082159',
                'Кит-Кат-343424',
            )
        );

        $tmpDir = '/tmp/' . uniqid('lighthouse_');
        mkdir($tmpDir);
        $file1 = $this->copyFixtureFileToDir(
            'Integration/Set10/ImportSales/purchases-14-05-2012_9-18-29.xml',
            $tmpDir
        );
        $file2 = $this->copyFixtureFileToDir(
            'Integration/Set10/ImportSales/purchases-13-09-2013_15-09-26.xml',
            $tmpDir
        );

        $this->createConfig(Set10Import::URL_CONFIG_NAME, 'file://' . $tmpDir);

        $command = $this->getContainer()->get('lighthouse.core.command.integration.sales_import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $display = $commandTester->getDisplay();

        $this->assertContains(basename($file1), $display);
        $this->assertContains("...\n", $display);
        $this->assertContains(basename($file2), $display);
        $this->assertContains(".V............V.....\n", $display);

        $this->assertFileNotExists($file1);
        $this->assertFileNotExists($file2);
        rmdir($tmpDir);
    }

    /**
     * @param string $file
     * @param string $dir
     * @return string
     */
    protected function copyFixtureFileToDir($file, $dir)
    {
        $source = $this->getFixtureFilePath($file);
        $destination = $dir . '/' . uniqid('purchases-') . '.xml';
        copy($source, $destination);
        return $destination;
    }
}

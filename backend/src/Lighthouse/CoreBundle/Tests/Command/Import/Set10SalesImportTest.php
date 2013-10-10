<?php

namespace Lighthouse\CoreBundle\Tests\Command\Import;

use Lighthouse\CoreBundle\Command\Import\Set10SalesImport;
use Lighthouse\CoreBundle\Integration\Set10\Import\Set10Import;
use Lighthouse\CoreBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class Set10SalesImportTest extends WebTestCase
{
    /**
     * @var string
     */
    protected $prefix = 'lighthouse';

    protected function tearDown()
    {
        $this->clearTempFiles();
        parent::tearDown();
    }

    protected function clearTempFiles()
    {
        $tmp = new \DirectoryIterator('/tmp/');
        /* @var \DirectoryIterator $dir */
        foreach ($tmp as $dir) {
            if ($dir->isDir() && 0 === strpos($dir->getFilename(), $this->prefix)) {
                $it = new \RecursiveDirectoryIterator($dir->getPathname());
                foreach (new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
                    if ($file->isFile()) {
                        unlink($file->getPathname());
                    }
                }
                rmdir($dir->getPathname());
            }
        }
    }

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

        $tmpDir = $this->createTempDir();
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
    }

    /**
     * @expectedException \Lighthouse\CoreBundle\Exception\RuntimeException
     * @expectedExceptionMessage Failed to read directory
     * @dataProvider invalidDirectoryProvider
     */
    public function testInvalidDirectory($url)
    {
        $this->createConfig(Set10Import::URL_CONFIG_NAME, $url);

        $command = $this->getContainer()->get('lighthouse.core.command.integration.sales_import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array());
    }

    /**
     * @return array
     */
    public function invalidDirectoryProvider()
    {
        return array(
            array('file://invalid/path'),
            //array('smb://invalid.host/invalid/path'),
        );
    }

    /**
     * @expectedException \Lighthouse\CoreBundle\Exception\RuntimeException
     * @expectedExceptionMessage Failed to read directory
     */
    public function testInvalidDirectoryIsFile()
    {
        $tmpDir = $this->createTempDir();
        $file1 = $this->copyFixtureFileToDir(
            'Integration/Set10/ImportSales/purchases-14-05-2012_9-18-29.xml',
            $tmpDir
        );

        $this->createConfig(Set10Import::URL_CONFIG_NAME, 'file://' . $file1);

        $command = $this->getContainer()->get('lighthouse.core.command.integration.sales_import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array());
    }

    public function testImportInvalidXmlFile()
    {
        $tmpDir = $this->createTempDir();
        $file1 = $this->copyFixtureFileToDir(
            'Integration/Set10/ImportSales/purchases-invalid.xml',
            $tmpDir
        );

        $this->createConfig(Set10Import::URL_CONFIG_NAME, 'file://' . $tmpDir);

        /* @var Set10SalesImport $command */
        $command = $this->getContainer()->get('lighthouse.core.command.integration.sales_import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array());

        $display = $commandTester->getDisplay();
        $this->assertContains('Failed to import sales', $display);
        $this->assertContains('Deleting "' . $this->prefix, $display);

        $this->assertFileNotExists($file1);
    }

    /**
     * @return string
     */
    protected function createTempDir()
    {
        $tmpDir = '/tmp/' . uniqid($this->prefix) . '/';
        mkdir($tmpDir);
        return $tmpDir;
    }

    /**
     * @param string $file
     * @param string $dir
     * @return string
     */
    protected function copyFixtureFileToDir($file, $dir)
    {
        $source = $this->getFixtureFilePath($file);
        $destination = $dir . '/' . uniqid($this->prefix) . '.xml';
        copy($source, $destination);
        return $destination;
    }
}

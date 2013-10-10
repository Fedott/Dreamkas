<?php

namespace Lighthouse\CoreBundle\Tests\Samba;

use Lighthouse\CoreBundle\Test\ContainerAwareTestCase;
use Lighthouse\CoreBundle\Samba\Samba as SambaBase;

class Samba extends ContainerAwareTestCase
{
	public function onConsecutiveCallsArray(array $array)
	{
		return new \PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls($array);
	}

    /**
     * @param $url
     * @param $expectedParsedUrl
     *
     * @dataProvider parserUrlProvider
     */
    public function testParseUrlMethod($url, $expectedParsedUrl)
    {
        $expectedParsedUrl = $expectedParsedUrl + array(
            'type' => 'path',
            'path' => 'to\dir',
            'host' => 'host',
            'user' => 'user',
            'pass' => 'password',
            'domain' => '',
            'share' => 'base_path',
            'port' => 139,
            'scheme' => 'smb',
        );

        $samba = new SambaBase();

        $parsedUrl = $samba->parseUrl($url);

        $this->assertEquals($expectedParsedUrl, $parsedUrl);
    }

    public function parserUrlProvider()
    {
        return array(
            'full base url' => array(
                "smb://user:password@host/base_path/to/dir",
                array(),
            ),
            'full base url with file' => array(
                "smb://user:password@host/base_path/to/dir/file.doc",
                array(
                    'path' => 'to\dir\file.doc',
                ),
            ),
            'base url without password' => array(
                "smb://user@host/base_path/to/dir",
                array(
                    'pass' => '',
                ),
            ),
            'base url without user and password' => array(
                "smb://host/base_path/to/dir",
                array(
                    'user' => '',
                    'pass' => '',
                ),
            ),
            'base url with port' => array(
                "smb://user:password@host:222/base_path/to/dir",
                array(
                    'port' => '222',
                ),
            ),
            'base url with port and domain' => array(
                "smb://domain.local;user:password@host:222/base_path/to/dir",
                array(
                    'port' => '222',
                    'domain' => 'domain.local',
                ),
            ),
            'base url without path' => array(
                "smb://user:password@host/base_path",
                array(
                    'path' => '',
                    'type' => 'share',
                ),
            ),
            'url without share' => array(
                "smb://user:password@host",
                array(
                    'path' => '',
                    'share' => '',
                    'type' => 'host',
                ),
            ),
            'base url empty' => array(
                "",
                array(
                    'user' => '',
                    'pass' => '',
                    'domain' => '',
                    'host' => '',
                    'share' => '',
                    'path' => '',
                    'type' => '**error**',
                    'scheme' => '',
                ),
            ),
        );
    }

    public function testLookMethod()
    {
        $url = "smb://user:password@host/base_path/to/dir/file.doc";

        $sambaMock = $this->getMock('\Lighthouse\CoreBundle\Samba\SambaStreamWrapper', array('client'));

        $parsedUrl = $sambaMock->parseUrl($url);

        $sambaMock
            ->expects($this->once())
            ->method('client')
            ->with($this->equalTo("-L 'host'"), $this->equalTo($parsedUrl));

        $sambaMock->look($parsedUrl);
    }

    public function testExecuteMethod()
    {
        $url = "smb://user:password@host/base_path/to/dir/file.doc";

        $sambaMock = $this->getMock('\Lighthouse\CoreBundle\Samba\SambaStreamWrapper', array('client'));

        $parsedUrl = $sambaMock->parseUrl($url);

        $expectedClientParams = "-d 0 '//host/base_path' -c 'test_command'";

        $sambaMock
            ->expects($this->once())
            ->method('client')
            ->with($this->equalTo($expectedClientParams), $this->equalTo($parsedUrl));

        $sambaMock->execute('test_command', $parsedUrl);
    }

    public function testUnlinkMethod()
    {
        $url = "smb://user:password@host/base_path/to/dir/file.doc";

        $sambaMock = $this->getMock('\Lighthouse\CoreBundle\Samba\SambaStreamWrapper', array('execute'));
        $parsedUrl = $sambaMock->parseUrl($url);

        $expectedExecuteCommand = 'del "to\dir\file.doc"';

        $sambaMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo($expectedExecuteCommand), $this->equalTo($parsedUrl));

        $sambaMock->unlink($url);
    }

    public function testRenameMethod()
    {
        $url = "smb://user:password@host/base_path/to/dir/file.doc";
        $urlNew = "smb://user:password@host/base_path/to/dir/file_new.doc";

        $sambaMock = $this->getMock('\Lighthouse\CoreBundle\Samba\SambaStreamWrapper', array('execute'));
        $parsedUrl = $sambaMock->parseUrl($url);
        $parsedUrlNew = $sambaMock->parseUrl($urlNew);

        $expectedExecuteCommand = 'rename "to\dir\file.doc" "to\dir\file_new.doc"';

        $sambaMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo($expectedExecuteCommand), $this->equalTo($parsedUrlNew));

        $sambaMock->rename($url, $urlNew);
    }

    public function testMkDirMethod()
    {
        $url = "smb://user:password@host/base_path/to/dir";

        $sambaMock = $this->getMock('\Lighthouse\CoreBundle\Samba\SambaStreamWrapper', array('execute'));
        $parsedUrl = $sambaMock->parseUrl($url);

        $expectedExecuteCommand = 'mkdir "to\dir"';

        $sambaMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo($expectedExecuteCommand), $this->equalTo($parsedUrl));

        $sambaMock->mkdir($url, '', '');
    }

    public function testRmDirMethod()
    {
        $url = "smb://user:password@host/base_path/to/dir";

        $sambaMock = $this->getMock('\Lighthouse\CoreBundle\Samba\SambaStreamWrapper', array('execute'));
        $parsedUrl = $sambaMock->parseUrl($url);

        $expectedExecuteCommand = 'rmdir "to\dir"';

        $sambaMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->equalTo($expectedExecuteCommand), $this->equalTo($parsedUrl));

        $sambaMock->rmdir($url);
    }

    public function testStatCacheMethods()
    {
        $urlFile = "smb://user:password@host/base_path/to/dir/file.doc";
        $urlDir = "smb://user:password@host/base_path/to/dir";

        $sambaMock = $this->getMock('\Lighthouse\CoreBundle\Samba\SambaStreamWrapper', array('execute'));

        $this->assertFalse($sambaMock->getstatcache($urlFile));
        $this->assertFalse($sambaMock->getstatcache($urlDir));

        $infoFile = array(
            'attr' => 'F',
            'size' => 4,
            'time' => 777,
        );
        $statFile = stat("/etc/passwd");
        $statFile[7] = $statFile['size'] = $infoFile['size'];
        $statFile[8]
            = $statFile[9]
            = $statFile[10]
            = $statFile['atime']
            = $statFile['mtime']
            = $statFile['ctime']
            = $infoFile['time'];

        $infoDir = $infoFile;
        $infoDir['attr'] = 'D';
        $statDir = stat("/tmp");
        $statDir[7] = $statDir['size'] = $infoDir['size'];
        $statDir[8]
            = $statDir[9]
            = $statDir[10]
            = $statDir['atime']
            = $statDir['mtime']
            = $statDir['ctime']
            = $infoDir['time'];

        $this->assertEquals($statFile, $sambaMock->addstatcache($urlFile, $infoFile));
        $this->assertEquals($statDir, $sambaMock->addstatcache($urlDir, $infoDir));

        $this->assertEquals($statFile, $sambaMock->getstatcache($urlFile));
        $this->assertEquals($statDir, $sambaMock->getstatcache($urlDir));

        $sambaMock->clearstatcache($urlFile);

        $this->assertFalse($sambaMock->getstatcache($urlFile));
        $this->assertEquals($statDir, $sambaMock->getstatcache($urlDir));

        $this->assertEquals($statFile, $sambaMock->addstatcache($urlFile, $infoFile));
        $sambaMock->clearstatcache();
        $this->assertFalse($sambaMock->getstatcache($urlFile));
        $this->assertFalse($sambaMock->getstatcache($urlDir));
    }

    public function testClientMethod()
    {
        $urlFile = "smb://user:password@host/base_path/to/dir/file.doc";
	    $urlDir = "smb://user:password@host/base_path/to/dir";

        $sambaMock = $this->getMock(
            '\Lighthouse\CoreBundle\Samba\SambaStreamWrapper',
            array(
                'getProcessResource',
                'fgets',
                'closeProcessResource',
            )
        );

        $sambaMock
            ->expects($this->any())
            ->method('fgets')
            ->will($this->onConsecutiveCalls(
		            "Anonymous login successful",
		            "Domain=[MYGROUP] OS=[Unix] Server=[Samba 3.0.33-3.39.el5_8]",
		            "",
		            "\tSharename       Type      Comment",
		            "\t---------       ----      -------",
		            "\tIPC$            IPC       IPC Service (Centrum Server Lighthouse)",
		            "\tcentrum         Disk      Centrum ERP integration",
		            "Anonymous login successful",
		            "Domain=[MYGROUP] OS=[Unix] Server=[Samba 3.0.33-3.39.el5_8]",
		            "",
		            "\tServer               Comment",
		            "\t---------            -------",
		            "\tVM6                  Centrum Server Lighthouse",
		            "",
		            "\tWorkgroup            Master",
		            "\t---------            -------",
		            "\tCMAG                 SHOP1",
		            "\tMYGROUP              VM6",
		            false
	        ));
	    $expectedLookInfo = array(
		    "disk" => array("centrum"),
		    "server" => array("vm6"),
		    "workgroup" => array("cmag", "mygroup"),
	    );

        $parsedUrlFile = $sambaMock->parseUrl($urlFile);
	    $parsedUrlDir = $sambaMock->parseUrl($urlDir);

        $lookInfo = $sambaMock->client('-L test.host', $parsedUrlFile);
        $this->assertEquals($expectedLookInfo, $lookInfo);

		$openDirInfo = <<<EOF
Anonymous login successful
Domain=[MYGROUP] OS=[Unix] Server=[Samba 3.0.33-3.39.el5_8]
  .                                   D        0  Fri Sep 13 11:13:28 2013
  ..                                  D        0  Thu Sep  5 16:54:33 2013
  success                             D        0  Thu Oct  3 12:42:46 2013
  test                                A        2  Fri Jun 28 21:13:51 2013
  error                               D        0  Wed Sep 11 18:53:11 2013
  tmp                                 D        0  Thu Oct  3 12:42:46 2013
  source                              D        0  Thu Oct  3 12:42:46 2013
  catalog-goods_1234-13-09-2013_11-30-14.xml      A     1120  Fri Sep 13 11:29:13 2013
  catalog-goods_1378998029.xml        A       70  Thu Sep 12 19:00:30 2013
  catalog-goods_1379058741.xml        A     3917  Fri Sep 13 11:52:22 2013

                37382 blocks of size 524288. 29328 blocks available
EOF;
	    $openDirInfo = explode("\n", $openDirInfo);
	    $openDirInfo[] = false;

	    $expectedDirInfo = $this->getDirInfoArray();

	    $sambaMock = $this->getMock(
		    '\Lighthouse\CoreBundle\Samba\SambaStreamWrapper',
		    array(
			    'getProcessResource',
			    'fgets',
			    'closeProcessResource',
		    )
	    );

	    $sambaMock
		    ->expects($this->any())
		    ->method('fgets')
		    ->will($this->onConsecutiveCallsArray($openDirInfo));

	    $dirInfo = $sambaMock->execute('dir "' . $parsedUrlDir['path'] . '\*""', $parsedUrlDir);

	    $this->assertEquals($expectedDirInfo, $dirInfo);
    }

	public function testDirOpenDirMethod()
	{
		$urlFile = "smb://user:password@host/base_path/to/dir/file.doc";
		$urlDir = "smb://user:password@host/base_path/to/dir";
		$urlHost = "smb://user:password@host";

		$sambaMock = $this->getMock(
			'\Lighthouse\CoreBundle\Samba\SambaStreamWrapper',
			array('look', 'execute')
		);

		$lookInfo = array(
			"disk" => array("centrum"),
			"server" => array("vm6"),
			"workgroup" => array("cmag", "mygroup"),
		);

		$sambaMock
			->expects($this->once())
			->method('look')
			->will($this->returnValue($lookInfo));

		$sambaMock->dir_opendir($urlHost, array());

		$this->assertEquals(array("centrum"), $sambaMock->dir);


		$sambaMock
			->expects($this->any())
			->method('execute')
			->will($this->returnValue($this->getDirInfoArray()));

		$sambaMock->dir_opendir($urlDir, array());

		$expectedDir = array(
			'success',
			'test',
			'error',
			'tmp',
			'source',
			'catalog-goods_1234-13-09-2013_11-30-14.xml',
			'catalog-goods_1378998029.xml',
			'catalog-goods_1379058741.xml',
		);
		$this->assertEquals($expectedDir , $sambaMock->dir);
	}

	/**
	 * @return array
	 */
	public function getDirInfoArray()
	{
		$expectedDirInfo = array(
			'info' => array(
				'success' => array(
					'success',
					'folder',
					'attr' => 'D',
					'size' => 0,
					'time' => 1380789766,
				),
				'test' => array(
					'test',
					'file',
					'attr' => 'A',
					'size' => 2,
					'time' => 1372439631,
				),
				'error' => array(
					'error',
					'folder',
					'attr' => 'D',
					'size' => 0,
					'time' => 1378911191,
				),
				'tmp' => array(
					'tmp',
					'folder',
					'attr' => 'D',
					'size' => 0,
					'time' => 1380789766,
				),
				'source' => array(
					'source',
					'folder',
					'attr' => 'D',
					'size' => 0,
					'time' => 1380789766,
				),
				'catalog-goods_1234-13-09-2013_11-30-14.xml' => array(
					'catalog-goods_1234-13-09-2013_11-30-14.xml',
					'file',
					'attr' => 'A',
					'size' => 1120,
					'time' => 1379057353,
				),
				'catalog-goods_1378998029.xml' => array(
					'catalog-goods_1378998029.xml',
					'file',
					'attr' => 'A',
					'size' => 70,
					'time' => 1378998030,
				),
				'catalog-goods_1379058741.xml' => array(
					'catalog-goods_1379058741.xml',
					'file',
					'attr' => 'A',
					'size' => 3917,
					'time' => 1379058742,
				),
			),
			'folder' => array(
				'success',
				'error',
				'tmp',
				'source'
			),
			'file' => array(
				'test',
				'catalog-goods_1234-13-09-2013_11-30-14.xml',
				'catalog-goods_1378998029.xml',
				'catalog-goods_1379058741.xml',
			),
		);
		return $expectedDirInfo;
	}
}

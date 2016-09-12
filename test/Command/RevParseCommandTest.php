<?php

use PHPGit\Git;
use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__.'/../BaseTestCase.php';

class RevParseCommandTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory.'/test.txt', '');
        $git->add('test.txt');
        $git->commit('Initial commit');
    }

    public function testRevParse()
    {
        $git = new Git();
        $git->setRepository($this->directory);
        $result = $git->revParse('master');

        $this->assertCount(1, $result);
        $this->assertEquals($git->log()[0]->hash, $result[0]);
    }

    public function testRevParseShort()
    {
        $git = new Git();
        $git->setRepository($this->directory);

        $hash   = $git->log()[0]->hash;
        $result = $git->revParse('master', ['short' => 10]);

        $this->assertCount(1, $result);
        $this->assertEquals(10, strlen($result[0]));
        $this->assertStringStartsWith($result[0], $hash);

        $result = $git->revParse('master', ['short' => true]);

        $this->assertCount(1, $result);
        $this->assertStringStartsWith($result[0], $hash);
    }

    public function testRevParseCurrentBranch()
    {
        $git = new Git();
        $git->setRepository($this->directory);
        $branch = $git->revParse->currentBranch();

        $this->assertEquals('master', $branch);
    }

    public function testRevParseIsRev()
    {
        $git = new Git();
        $git->setRepository($this->directory);

        $this->assertTrue($git->revParse->isRev('master'));
        $this->assertFalse($git->revParse->isRev('hodor'));
    }
}

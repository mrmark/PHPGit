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

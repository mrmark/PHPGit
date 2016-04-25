<?php

use PHPGit\Git;

class GitTest extends PHPUnit_Framework_TestCase
{
    public function testGetVersion()
    {
        $git = new Git();
        $this->assertNotEmpty($git->getVersion());
    }

    public function testTimeout()
    {
        $git = new Git();
        $this->assertEquals(null, $git->getTimeout(), 'Default should be null');

        $git->setTimeout(60);
        $this->assertEquals(60, $git->getTimeout());

        $builder = $git->getProcessBuilder();
        $process = $builder->getProcess();
        $this->assertEquals(60, $process->getTimeout());
    }

    /**
     * @expectedException \PHPGit\Exception\GitException
     */
    public function testInvalidGitBinary()
    {
        $git = new Git();
        $git->setBin('/foo/bar');
        $git->getVersion();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testBadMethodCall()
    {
        $git = new Git();
        $git->foo();
    }
}

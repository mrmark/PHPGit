<?php

use PHPGit\Git;
use Symfony\Component\Process\Process;

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

    public function testRun()
    {
        $git = new Git();
        $git->run(new Process('echo "Hi!"'));
    }

    /**
     * @expectedException \PHPGit\Exception\GitException
     */
    public function testRunFail()
    {
        $git = new Git();
        $git->run(new Process('php -r "exit(1);"'));
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

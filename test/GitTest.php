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
        $git    = new Git();
        $output = $git->run(new Process('echo "Hi!"'));

        $this->assertEquals('Hi!', trim($output));
    }

    public function testRunWithCallback()
    {
        $called = false;

        $git = new Git();
        $git->setRunCallback(function (Process $process) use (&$called) {
            $process->run();
            $called = true;
        });

        $output = $git->run(new Process('echo "Hi!"'));

        $this->assertEquals('Hi!', trim($output));
        $this->assertTrue($called);
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

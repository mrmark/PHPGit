<?php

use PHPGit\Git;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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

    public function testLogger()
    {
        $git    = new Git();
        $logger = new NullLogger();

        $this->assertFalse($git->hasLogger());

        $git->setLogger($logger);
        $this->assertSame($logger, $git->getLogger());
        $this->assertTrue($git->hasLogger());
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

    public function testRunLogging()
    {
        $git = new Git();

        $logger = $this->prophesize(LoggerInterface::class);

        $git->setLogger($logger->reveal());
        $git->run(new Process('echo "Hi!"'));

        $logger->info(Argument::type('string'))->shouldHaveBeenCalledTimes(2);
        $logger->debug(Argument::type('string'))->shouldHaveBeenCalled();
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

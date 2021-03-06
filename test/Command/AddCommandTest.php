<?php

use PHPGit\Git;
use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__.'/../BaseTestCase.php';

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class AddCommandTest extends BaseTestCase
{
    public function testAdd()
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir($this->directory);

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory.'/test.txt', 'foo');
        $filesystem->dumpFile($this->directory.'/test.md', '**foo**');

        $git->add('test.txt');
        $git->add(['test.md'], ['force' => true]);
    }

    /**
     * @expectedException \PHPGit\Exception\GitException
     * @expectedExceptionCode 128
     */
    public function testException()
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->add('foo');
    }
}

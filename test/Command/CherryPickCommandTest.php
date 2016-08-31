<?php

use PHPGit\Git;
use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__.'/../BaseTestCase.php';

class CherryPickCommandTest extends BaseTestCase
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

    public function testCheeryPick()
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->setRepository($this->directory);
        $git->checkout->create('test');

        $filesystem->dumpFile($this->directory.'/test.txt', 'foo');

        $git->add('test.txt');
        $git->commit('Pick me');

        $log  = $git->log();
        $hash = $log[0]['hash'];

        $git->checkout('master');
        $git->cherryPick($hash, ['x' => true]);

        $this->assertEquals('foo', file_get_contents($this->directory.'/test.txt'));

        $log = $git->log();

        $this->assertCount(2, $log);
        $this->assertEquals('Pick me', $log[0]['title']);
        $this->assertNotEquals($hash, $log[0]['hash']);
    }
}

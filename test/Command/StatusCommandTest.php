<?php

use PHPGit\Command\StatusCommand;
use PHPGit\Git;
use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__.'/../BaseTestCase.php';

class StatusCommandTest extends BaseTestCase
{
    public function testStatus()
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory.'/item1.txt', '1');
        $filesystem->dumpFile($this->directory.'/item2.txt', '2');
        $filesystem->dumpFile($this->directory.'/item3.txt', '3');

        $git->add('item1.txt');
        $git->add('item2.txt');

        $filesystem->dumpFile($this->directory.'/item1.txt', '1-1');

        $status = $git->status();

        $this->assertEquals([
            'branch'  => 'master',
            'changes' => [
                ['file' => 'item1.txt', 'index' => StatusCommand::ADDED, 'work_tree' => StatusCommand::MODIFIED],
                ['file' => 'item2.txt', 'index' => StatusCommand::ADDED, 'work_tree' => StatusCommand::UNMODIFIED],
                ['file' => 'item3.txt', 'index' => StatusCommand::UNTRACKED, 'work_tree' => StatusCommand::UNTRACKED],
            ],
        ], $status);

        $filesystem->dumpFile($this->directory.'/folder/item1.txt', '1-1');
        $git->add('folder');

        $status = $git->status('folder');

        $this->assertEquals([
            'branch'  => 'master',
            'changes' => [
                ['file' => 'folder/item1.txt', 'index' => StatusCommand::ADDED, 'work_tree' => StatusCommand::UNMODIFIED],
            ],
        ], $status);
    }

    public function testDetachedHeadStatus()
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory.'/item1.txt', '1');
        $git->add('item1.txt');
        $git->commit('initial commit');
        $logs = $git->log();
        $hash = $logs[0]->hash;

        $git->checkout($hash);
        $status = $git->status();
        $this->assertEquals(null, $status['branch']);
    }

    public function testTrackingBranchStatus()
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->clone('https://github.com/kzykhys/Text.git', $this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory.'/test.txt', '1');
        $git->add('test.txt');
        $git->commit('test');

        $status = $git->status();
        $this->assertEquals('master', $status['branch']);
    }
}

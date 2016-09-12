<?php

use PHPGit\Git;

require_once __DIR__.'/../BaseTestCase.php';

class RemoteCommandTest extends BaseTestCase
{
    public function testRemote()
    {
        $git = new Git();
        $git->clone('https://github.com/kzykhys/Text.git', $this->directory);
        $git->setRepository($this->directory);

        $remotes = $git->remote();

        $this->assertCount(1, $remotes);
        $this->assertArrayHasKey('origin', $remotes);
        $this->assertEquals('origin', $remotes['origin']->name);
        $this->assertEquals('https://github.com/kzykhys/Text.git', $remotes['origin']->fetch);
        $this->assertEquals('https://github.com/kzykhys/Text.git', $remotes['origin']->push);
    }

    public function testRemoteAdd()
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');

        $remotes = $git->remote();

        $this->assertCount(1, $remotes);
        $this->assertArrayHasKey('origin', $remotes);
        $this->assertEquals('origin', $remotes['origin']->name);
        $this->assertEquals('https://github.com/kzykhys/Text.git', $remotes['origin']->fetch);
        $this->assertEquals('https://github.com/kzykhys/Text.git', $remotes['origin']->push);
    }

    public function testRemoteRename()
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
        $git->remote->rename('origin', 'upstream');

        $remotes = $git->remote();

        $this->assertCount(1, $remotes);
        $this->assertArrayHasKey('upstream', $remotes);
        $this->assertEquals('upstream', $remotes['upstream']->name);
        $this->assertEquals('https://github.com/kzykhys/Text.git', $remotes['upstream']->fetch);
        $this->assertEquals('https://github.com/kzykhys/Text.git', $remotes['upstream']->push);
    }

    public function testRemoteRm()
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
        $git->remote->rm('origin');

        $remotes = $git->remote();
        $this->assertEquals([], $remotes);
    }

    public function testRemoteShow()
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');

        $this->assertNotEmpty($git->remote->show('origin'));
    }

    public function testRemotePrune()
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
        $git->remote->prune('origin');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testBadMethodCall()
    {
        $git = new Git();
        $git->remote->foo();
    }
}

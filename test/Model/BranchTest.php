<?php

use PHPGit\Model\Branch;

class BranchTest extends PHPUnit_Framework_TestCase
{
    public function testBranch()
    {
        $branch = new Branch('foo/bar');
        $this->assertTrue($branch->isLocal());
        $this->assertFalse($branch->isRemote());
        $this->assertEquals('foo/bar', $branch->getName());

        $branch = new Branch('remotes/origin/foo/bar');
        $this->assertFalse($branch->isLocal());
        $this->assertTrue($branch->isRemote());
        $this->assertEquals('foo/bar', $branch->getName());
        $this->assertEquals('origin', $branch->getRemote());
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetRemoteOnLocalBranch()
    {
        $branch = new Branch('foo/bar');
        $branch->getRemote();
    }
}

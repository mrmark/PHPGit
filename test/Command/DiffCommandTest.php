<?php

use PHPGit\Git;
use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__.'/../BaseTestCase.php';

class DiffCommandTest extends BaseTestCase
{
    public function testDiff()
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory.'/README.md', 'hello');
        $git->add('README.md');
        $git->commit('Initial commit');

        $hash = $git->revParse('HEAD')[0];

        $filesystem->dumpFile($this->directory.'/README.md', 'hello2');
        $git->add('README.md');
        $git->commit('Fixes README');

        $expected = ' README.md | 2 +-
 1 file changed, 1 insertion(+), 1 deletion(-)
';

        $output = $git->diff($hash.'..', 'README.md', ['stat' => true]);
        $this->assertEquals($expected, $output);
    }
}

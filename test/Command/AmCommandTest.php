<?php

use PHPGit\Git;
use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__.'/../BaseTestCase.php';

class AmCommandTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory.'/bin/test.txt', '');
        $git->add('bin/test.txt');
        $git->commit('Initial commit');
    }

    public function testAm()
    {
        $content = <<<'EOT'
From 38c2c0499298cd2ecc1effaf466d5273f63c3c94 Mon Sep 17 00:00:00 2001
From: John Doe <john@example.com>
Date: Tue, 6 Sep 2016 14:36:42 -0700
Subject: [PATCH] I am a patch

---
 bin/test.txt | 1 +
 1 file changed, 1 insertion(+)

diff --git a/bin/test.txt b/bin/test.txt
index e69de29..1910281 100644
--- a/bin/test.txt
+++ b/bin/test.txt
@@ -0,0 +1 @@
+foo
\ No newline at end of file
-- 
2.9.0

EOT;
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->directory.'/file.patch', $content);

        $git = new Git();
        $git->setRepository($this->directory);
        $git->am($this->directory.'/file.patch');

        $this->assertCount(2, $git->log());
    }

    public function testAmDirectory()
    {
        $content = <<<'EOT'
From 38c2c0499298cd2ecc1effaf466d5273f63c3c94 Mon Sep 17 00:00:00 2001
From: John Doe <john@example.com>
Date: Tue, 6 Sep 2016 14:36:42 -0700
Subject: [PATCH] I am a patch

---
 test.txt | 1 +
 1 file changed, 1 insertion(+)

diff --git a/test.txt b/test.txt
index e69de29..1910281 100644
--- a/test.txt
+++ b/test.txt
@@ -0,0 +1 @@
+foo
\ No newline at end of file
-- 
2.9.0

EOT;
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->directory.'/file.patch', $content);

        $git = new Git();
        $git->setRepository($this->directory);
        $git->am($this->directory.'/file.patch', ['directory' => 'bin']);

        $this->assertCount(2, $git->log());
    }
}

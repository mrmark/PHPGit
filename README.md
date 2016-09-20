PHPGit - A Git wrapper for PHP
==============================

[![Build Status](https://travis-ci.org/mrmark/PHPGit.svg?branch=master)](https://travis-ci.org/mrmark/PHPGit)
[![Coverage Status](https://coveralls.io/repos/github/mrmark/PHPGit/badge.svg?branch=master)](https://coveralls.io/github/mrmark/PHPGit?branch=master)

Disclaimer
----------

This is a fork from from the [original project](https://github.com/kzykhys/PHPGit) which, at the time of writing this, appears to
be abandoned.  This fork is for my own personal use, so please understand that contributions may not be accepted and that the
project might be customized for my own personal use cases.  So, yeah, you probably should not use it, cheers!

Requirements
------------

* PHP5.5
* Git

Basic Usage
-----------

``` php
<?php

require __DIR__ . '/vendor/autoload.php';

$git = new PHPGit\Git();
$git->clone('https://github.com/kzykhys/PHPGit.git', '/path/to/repo');
$git->setRepository('/path/to/repo');
$git->remote->add('production', 'git://example.com/your/repo.git');
$git->add('README.md');
$git->commit('Adds README.md');
$git->checkout('release');
$git->merge('master');
$git->push();
$git->push('production', 'release');
$git->tag->create('v1.0.1', 'release');

foreach ($git->tree('release') as $object) {
    if ($object['type'] == 'blob') {
        echo $git->show($object['file']);
    }
}
```

License
-------

The MIT License

Author
------

Kazuyuki Hayashi (@kzykhys)

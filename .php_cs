<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->path('src')
    ->path('test');

return Symfony\CS\Config\Config::create()
    ->fixers([
        'align_equals',
        'align_double_arrow',
        'ordered_use',
        'short_array_syntax',
        '-psr0',
        '-phpdoc_short_description',
        '-phpdoc_separation',
    ])
    ->finder($finder);
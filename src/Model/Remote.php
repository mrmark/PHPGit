<?php

namespace PHPGit\Model;

/**
 * A git remote.
 *
 * Note that is not any sort of technical mapping of a git internal object,
 * just a plain model with data.
 */
class Remote
{
    /**
     * The author name.
     *
     * @var string
     */
    public $name;

    /**
     * The remote's fetch URL.
     *
     * @var string
     */
    public $fetch;

    /**
     * The remote's push URL.
     *
     * @var string
     */
    public $push;

    /**
     * @param string $name
     * @param string $fetch
     * @param string $push
     */
    public function __construct($name, $fetch = '', $push = '')
    {
        $this->name  = $name;
        $this->fetch = $fetch;
        $this->push  = $push;
    }
}

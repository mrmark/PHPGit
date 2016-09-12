<?php

namespace PHPGit\Model;

/**
 * A git branch.
 *
 * Note that is not any sort of technical mapping of a git internal object,
 * just a plain model with data.
 */
class Branch
{
    /**
     * The branch name.
     *
     * @var string
     */
    public $name = '';

    /**
     *  If the branch is the current branch or not.
     *
     * @var bool
     */
    public $current = false;

    /**
     * The branches HEAD commit summary.
     *
     * @var string
     */
    public $title = '';

    /**
     * Branch alias, if this is set, then hash and title are empty.
     *
     * @var string
     */
    public $alias = '';

    /**
     * The SHA.
     *
     * @var string
     */
    public $hash = '';
}

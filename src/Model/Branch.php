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
    public $name;

    /**
     *  If the branch is the current branch or not.
     *
     * @var bool
     */
    public $current;

    /**
     * The branches HEAD commit summary.
     *
     * @var string
     */
    public $title;

    /**
     * Branch alias, if this is set, then hash and title are empty.
     *
     * @var string
     */
    public $alias;

    /**
     * The SHA.
     *
     * @var string
     */
    public $hash;

    /**
     * @param string $name
     * @param string $hash
     * @param bool   $current
     * @param string $title
     * @param string $alias
     */
    public function __construct($name, $hash = '', $current = false, $title = '', $alias = '')
    {
        $this->name    = $name;
        $this->hash    = $hash;
        $this->current = $current;
        $this->title   = $title;
        $this->alias   = $alias;
    }

    /**
     * Determine if a branch is a remote branch or not.
     *
     * @return bool
     */
    public function isRemote()
    {
        return $this->parseName()[0] !== null;
    }

    /**
     * Determine if a branch is local or not.
     *
     * @return bool
     */
    public function isLocal()
    {
        return $this->parseName()[0] === null;
    }

    /**
     * Returns the name of the branch, without the "remotes/<remoteName>/" prefix if it is there.
     *
     * @return string
     */
    public function getName()
    {
        return $this->parseName()[1];
    }

    /**
     * Returns the remote name from within the branch name.
     *
     * Only call on remote branches.
     *
     * @return string
     */
    public function getRemote()
    {
        $remote = $this->parseName()[0];
        if ($remote === null) {
            throw new \LogicException(sprintf('The %s branch is not a remote branch', $this->name));
        }

        return $remote;
    }

    /**
     * Extract remote and branch names.
     *
     * @return array
     */
    private function parseName()
    {
        if (preg_match('/^remotes\/(?<remote>[^\/]*)\/(?<name>.*)$/', $this->name, $matches)) {
            return [$matches['remote'], $matches['name']];
        }

        return [null, $this->name];
    }
}

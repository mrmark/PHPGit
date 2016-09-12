<?php

namespace PHPGit\Model;

/**
 * A git log entry, AKA a commit.
 *
 * Note that is not any sort of technical mapping of a git internal object,
 * just a plain model with data.
 */
class Log
{
    /**
     * The author name.
     *
     * @var string
     */
    public $name;

    /**
     * The author email.
     *
     * @var string
     */
    public $email;

    /**
     * The commit date.
     *
     * Example format: Fri Jan 17 16:32:49 2014 +0900
     *
     * @var string
     */
    public $date;

    /**
     * Commit one line summary.
     *
     * @var string
     */
    public $title;

    /**
     * The SHA.
     *
     * @var string
     */
    public $hash;

    /**
     * @param string $name
     * @param string $email
     * @param string $date
     * @param string $title
     * @param string $hash
     */
    public function __construct($name, $email, $date, $title, $hash)
    {
        $this->name  = $name;
        $this->email = $email;
        $this->date  = $date;
        $this->title = $title;
        $this->hash  = $hash;
    }
}

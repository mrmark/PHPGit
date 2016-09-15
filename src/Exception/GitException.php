<?php

namespace PHPGit\Exception;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class GitException extends ProcessFailedException
{
    /**
     * @param Process $process The failed process
     */
    public function __construct(Process $process)
    {
        parent::__construct($process);
        $this->code = $process->getExitCode();
    }
}

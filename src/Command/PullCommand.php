<?php

namespace PHPGit\Command;

use PHPGit\Command;

/**
 * Fetch from and merge with another repository or a local branch - `git pull`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class PullCommand extends Command
{
    /**
     * @see \PHPGit\Git::pull()
     *
     * @param string $repository The "remote" repository that is the source of a fetch or pull operation
     * @param string $refspec    The format of a <refspec> parameter is an optional plus +,
     *                           followed by the source ref <src>, followed by a colon :, followed by the destination ref <dst>
     */
    public function __invoke($repository = null, $refspec = null)
    {
        $builder = $this->git->getProcessBuilder()
            ->add('pull');

        if ($repository) {
            $builder->add($repository);

            if ($refspec) {
                $builder->add($refspec);
            }
        }

        $this->git->run($builder->getProcess());
    }
}

<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Show the working tree status - `git status`.
 *
 *   = unmodified
 * M = modified
 * A = added
 * D = deleted
 * R = renamed
 * C = copied
 * U = updated but unmerged
 *
 * X          Y     Meaning
 * -------------------------------------------------
 * [MD]   not updated
 * M        [ MD]   updated in index
 * A        [ MD]   added to index
 * D         [ M]   deleted from index
 * R        [ MD]   renamed in index
 * C        [ MD]   copied in index
 * [MARC]           index and work tree matches
 * [ MARC]     M    work tree changed since index
 * [ MARC]     D    deleted in work tree
 * -------------------------------------------------
 * D           D    unmerged, both deleted
 * A           U    unmerged, added by us
 * U           D    unmerged, deleted by them
 * U           A    unmerged, added by them
 * D           U    unmerged, deleted by us
 * A           A    unmerged, both added
 * U           U    unmerged, both modified
 * -------------------------------------------------
 * ?           ?    untracked
 * !           !    ignored
 * -------------------------------------------------
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class StatusCommand extends Command
{
    const UNMODIFIED           = ' ';
    const MODIFIED             = 'M';
    const ADDED                = 'A';
    const DELETED              = 'D';
    const RENAMED              = 'R';
    const COPIED               = 'C';
    const UPDATED_BUT_UNMERGED = 'U';
    const UNTRACKED            = '?';
    const IGNORED              = '!';

    /**
     * @see \PHPGit\Git::status()
     *
     * @param array $options [optional] An array of options
     *
     * @return array
     */
    public function __invoke(array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('status')
            ->add('--porcelain')->add('-s')->add('-b')->add('--null');

        $this->addFlags($builder, $options);

        $process = $builder->getProcess();
        $result  = ['branch' => null, 'changes' => []];
        $output  = $this->git->run($process);

        list($branch, $changes) = preg_split('/(\0|\n)/', $output, 2);
        $lines                  = $this->split($changes, true);

        if (substr($branch, -11) == '(no branch)') {
            $result['branch'] = null;
        } elseif (preg_match('/([^ ]*)\.\.\..*?\[.*?\]$/', $branch, $matches)) {
            $result['branch'] = $matches[1];
        } elseif (preg_match('/ ([^ ]*)$/', $branch, $matches)) {
            $result['branch'] = $matches[1];
        }

        foreach ($lines as $line) {
            $result['changes'][] = [
                'file'      => substr($line, 3),
                'index'     => substr($line, 0, 1),
                'work_tree' => substr($line, 1, 1),
            ];
        }

        return $result;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'ignored' => false,
        ]);
    }
}

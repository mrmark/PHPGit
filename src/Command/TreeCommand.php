<?php

namespace PHPGit\Command;

use PHPGit\Command;

/**
 * List the contents of a tree object - `git ls-tree`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class TreeCommand extends Command
{
    /**
     * @see \PHPGit\Git::tree()
     *
     * @param string $branch The commit
     * @param string $path   The path
     *
     * @return array
     */
    public function __invoke($branch = 'master', $path = '')
    {
        $objects = [];
        $builder = $this->git->getProcessBuilder();
        $process = $builder->add('ls-tree')->add($branch.':'.$path)->getProcess();
        $output  = $this->git->run($process);
        $lines   = $this->split($output);

        $types = [
            'submodule' => 0,
            'tree'      => 1,
            'blob'      => 2,
        ];

        foreach ($lines as $line) {
            list($meta, $file)        = explode("\t", $line);
            list($mode, $type, $hash) = explode(' ', $meta);

            $objects[] = [
                'sort' => sprintf('%d:%s', $types[$type], $file),
                'mode' => $mode,
                'type' => $type,
                'hash' => $hash,
                'file' => $file,
            ];
        }

        return $objects;
    }
}

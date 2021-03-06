<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Model\Branch;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * List, create, or delete branches - `git branch`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class BranchCommand extends Command
{
    /**
     * @see \PHPGit\Git::branch()
     *
     * @param array $options An array of options
     *
     * @return Branch[]
     */
    public function __invoke(array $options = [])
    {
        $options  = $this->resolve($options);
        $branches = [];
        $builder  = $this->getProcessBuilder()
            ->add('-v')->add('--abbrev=7');

        if ($options['remotes']) {
            $builder->add('--remotes');
        }

        if ($options['all']) {
            $builder->add('--all');
        }

        if ($options['merged']) {
            $builder->add('--merged');

            if (is_string($options['merged'])) {
                $builder->add($options['merged']);
            }
        }

        $process = $builder->getProcess();
        $this->git->run($process);

        $lines = preg_split('/\r?\n/', rtrim($process->getOutput()), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($lines as $line) {
            preg_match('/(?<current>\*| ) (?<name>[^\s]+) +((?:->) (?<alias>[^\s]+)|(?<hash>[0-9a-z]{7,15}) (?<title>.*))/', $line, $matches);

            $branch = new Branch($matches['name']);

            $branch->current = ($matches['current'] == '*');

            if (isset($matches['hash'])) {
                $branch->hash  = $matches['hash'];
                $branch->title = $matches['title'];
            } else {
                $branch->alias = $matches['alias'];
            }

            $branches[$matches['name']] = $branch;
        }

        return $branches;
    }

    /**
     * Creates a new branch head named $branch which points to the current HEAD, or $startPoint if given.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->branch->create('bugfix');              // from current HEAD
     * $git->branch->create('patch-1', 'a092bf7s'); // from commit
     * $git->branch->create('1.0.x-fix', 'v1.0.2'); // from tag
     * ```
     *
     * Options:
     * - force (boolean) Reset $branch to $startPoint if $branch exists already
     *
     * @param string $branch     The name of the branch to create
     * @param string $startPoint The new branch head will point to this commit.
     *                           It may be given as a branch name, a commit-id, or a tag.
     *                           If this option is omitted, the current HEAD will be used instead
     * @param array  $options    An array of options
     */
    public function create($branch, $startPoint = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->getProcessBuilder();

        if ($options['force']) {
            $builder->add('-f');
        }

        $builder->add($branch);

        if ($startPoint) {
            $builder->add($startPoint);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Move/rename a branch and the corresponding reflog.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->branch->move('bugfix', '2.0');
     * ```
     *
     * Options:
     * - force (boolean) Move/rename a branch even if the new branch name already exists
     *
     * @param string $branch    The name of an existing branch to rename
     * @param string $newBranch The new name for an existing branch
     * @param array  $options   An array of options
     */
    public function move($branch, $newBranch, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->getProcessBuilder();

        if ($options['force']) {
            $builder->add('-M');
        } else {
            $builder->add('-m');
        }

        $builder->add($branch)->add($newBranch);
        $this->git->run($builder->getProcess());
    }

    /**
     * Delete a branch.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->branch->delete('2.0');
     * ```
     *
     * The branch must be fully merged in its upstream branch, or in HEAD if no upstream was set with --track or --set-upstream.
     *
     * Options:
     * - force (boolean) Delete a branch irrespective of its merged status
     *
     * @param string $branch  The name of the branch to delete
     * @param array  $options An array of options
     */
    public function delete($branch, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->getProcessBuilder();

        if ($options['force']) {
            $builder->add('-D');
        } else {
            $builder->add('-d');
        }

        $builder->add($branch);
        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'force'   => false,
            'all'     => false,
            'remotes' => false,
            'merged'  => null,
        ]);
    }

    /**
     * @return \Symfony\Component\Process\ProcessBuilder
     */
    protected function getProcessBuilder()
    {
        return $this->git->getProcessBuilder()
            ->add('branch');
    }
}

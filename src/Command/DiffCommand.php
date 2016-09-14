<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Exception\GitException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiffCommand extends Command
{
    /**
     * Show changes between commits, commit and working tree, etc.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $output = $git->diff('A..B');
     * ```
     *
     * ##### Options
     *
     * - **stat**      (_boolean_) Generate a diff stat
     * - **shortstat** (_boolean_) Output only the last line of the --stat format containing total number of modified files, as well as number of added and deleted lines
     * - **cached**    (_boolean_) Work on files staged in the index
     *
     * @param string $commit  Commit or commit range to diff, EG: 'A..B' or 'A' or 'A B", etc
     * @param string $path    Restrict diff to file path
     * @param array  $options [optional] An array of options {@see DiffCommand::setDefaultOptions}
     *
     * @throws GitException
     *
     * @return string
     */
    public function __invoke($commit = null, $path = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('diff');

        $this->addFlags($builder, $options);

        if ($commit) {
            $builder->add($commit);
        }
        if ($path) {
            $builder->add('--')->add($path);
        }

        return $this->git->run($builder->getProcess());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'stat'      => false,
            'shortstat' => false,
            'cached'    => false,
        ]);
    }
}

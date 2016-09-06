<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Exception\GitException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CherryPickCommand extends Command
{
    /**
     * Given existing commit, apply the change it introduces, recording a new commit.
     * This requires your working tree to be clean (no modifications from the HEAD commit).
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->cherryPick('abc123');
     * ```
     *
     * ##### Options
     *
     * - **x** (_boolean_) When recording the commit, append a line that says "(cherry picked from commit ...)" to the original commit message
     *
     * @param string $commit  The commit to pick
     * @param array  $options [optional] An array of options {@see CherryPickCommand::setDefaultOptions}
     *
     * @throws GitException
     *
     * @return bool
     */
    public function __invoke($commit, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('cherry-pick');

        if ($options['x']) {
            $builder->add('-x');
        }

        $builder->add($commit);
        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **x** (_boolean_) When recording the commit, append a line that says "(cherry picked from commit ...)" to the original commit message
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'x' => false,
        ]);
    }
}

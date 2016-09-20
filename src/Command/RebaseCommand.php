<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Forward-port local commits to the updated upstream head - `git rebase`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class RebaseCommand extends Command
{
    /**
     * @see \PHPGit\Git::rebase()
     *
     * @param string $upstream Upstream branch to compare against
     * @param string $branch   Working branch; defaults to HEAD
     * @param array  $options  An array of options
     */
    public function __invoke($upstream = null, $branch = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('rebase');

        if ($options['onto']) {
            $builder->add('--onto')->add($options['onto']);
        }

        if ($upstream) {
            $builder->add($upstream);
        }

        if ($branch) {
            $builder->add($branch);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Restart the rebasing process after having resolved a merge conflict.
     */
    public function continues()
    {
        $builder = $this->git->getProcessBuilder()
            ->add('rebase')
            ->add('--continue');

        $this->git->run($builder->getProcess());
    }

    /**
     * Abort the rebase operation and reset HEAD to the original branch.
     */
    public function abort()
    {
        $builder = $this->git->getProcessBuilder()
            ->add('rebase')
            ->add('--abort');

        $this->git->run($builder->getProcess());
    }

    /**
     * Restart the rebasing process by skipping the current patch.
     */
    public function skip()
    {
        $builder = $this->git->getProcessBuilder()
            ->add('rebase')
            ->add('--skip');

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'onto'         => null,
            'no-verify'    => false,
            'force-rebase' => false,
        ]);

        $resolver->setAllowedTypes('onto', ['null', 'string']);
    }
}

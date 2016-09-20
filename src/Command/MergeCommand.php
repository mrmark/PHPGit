<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Join two or more development histories together - `git merge`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class MergeCommand extends Command
{
    /**
     * @see \PHPGit\Git::merge()
     *
     * @param string|array|\Traversable $commit  Commits to merge into our branch
     * @param string                    $message Commit message to be used for the merge commit
     * @param array                     $options An array of options
     */
    public function __invoke($commit, $message = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('merge');

        $this->addFlags($builder, $options, ['no-ff', 'ff-only', 'rerere-autoupdate', 'squash']);

        if ($message) {
            $builder->add('-m')->add($message);
        }

        if (!is_array($commit) && !($commit instanceof \Traversable)) {
            $commit = [$commit];
        }
        foreach ($commit as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Abort the merge process and try to reconstruct the pre-merge state.
     *
     * ```php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * try {
     *     $git->merge('dev');
     * } catch (PHPGit\Exception\GitException $e) {
     *     $git->merge->abort();
     * }
     * ```
     */
    public function abort()
    {
        $builder = $this->git->getProcessBuilder()
            ->add('merge')
            ->add('--abort');

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'no-ff'             => false,
            'ff-only'           => false,
            'rerere-autoupdate' => false,
            'squash'            => false,

            'strategy'        => null,
            'strategy-option' => null,
        ]);
    }
}

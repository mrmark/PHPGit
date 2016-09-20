<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Checkout a branch or paths to the working tree - `git checkout`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class CheckoutCommand extends Command
{
    /**
     * @see \PHPGit\Git::checkout()
     *
     * @param string $branch  Branch to checkout
     * @param array  $options An array of options
     */
    public function __invoke($branch, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('checkout');

        $this->addFlags($builder, $options, ['force', 'merge']);

        $builder->add($branch);
        $this->git->run($builder->getProcess());
    }

    /**
     * Create a new branch and checkout.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->checkout->create('patch-1');
     * $git->checkout->create('patch-2', 'develop');
     * ```
     *
     * Options:
     * - force (boolean) Proceed even if the index or the working tree differs from HEAD
     *
     * @param string $branch     Branch to checkout
     * @param string $startPoint The name of a commit at which to start the new branch
     * @param array  $options    An array of options
     */
    public function create($branch, $startPoint = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('checkout')
            ->add('-b');

        $this->addFlags($builder, $options, ['force', 'merge']);

        $builder->add($branch);

        if ($startPoint) {
            $builder->add($startPoint);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Create a new orphan branch, named <new_branch>, started from <start_point> and switch to it.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->checkout->orphan('gh-pages');
     * ```
     *
     * Options:
     * - force (boolean) Proceed even if the index or the working tree differs from HEAD
     *
     * @param string $branch     Branch to checkout
     * @param string $startPoint The name of a commit at which to start the new branch
     * @param array  $options    An array of options
     */
    public function orphan($branch, $startPoint = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('checkout');

        $this->addFlags($builder, $options, ['force', 'merge']);

        $builder->add('--orphan')->add($branch);

        if ($startPoint) {
            $builder->add($startPoint);
        }

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'force' => false,
            'merge' => false,
        ]);
    }
}

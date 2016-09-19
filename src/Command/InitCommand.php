<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Create an empty git repository or reinitialize an existing one - `git init`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class InitCommand extends Command
{
    /**
     * Create an empty git repository or reinitialize an existing one.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->init('/path/to/repo1');
     * $git->init('/path/to/repo2', array('shared' => true, 'bare' => true));
     * ```
     *
     * ##### Options
     *
     * - **shared** (_boolean_) Specify that the git repository is to be shared amongst several users
     * - **bare**   (_boolean_) Create a bare repository
     *
     * @param string $path    The directory to create an empty repository
     * @param array  $options [optional] An array of options {@see InitCommand::setDefaultOptions}
     */
    public function __invoke($path, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('init');

        $this->addFlags($builder, $options, ['shared', 'bare']);

        $process = $builder->add($path)->getProcess();
        $this->git->run($process);
    }

    /**
     * {@inheritdoc}
     *
     * - **shared** (_boolean_) Specify that the git repository is to be shared amongst several users
     * - **bare**   (_boolean_) Create a bare repository
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'shared' => false,
            'bare'   => false,
        ]);
    }
}

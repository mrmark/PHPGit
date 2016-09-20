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
     * @see \PHPGit\Git::init()
     *
     * @param string $path    The directory to create an empty repository
     * @param array  $options An array of options
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

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'shared' => false,
            'bare'   => false,
        ]);
    }
}

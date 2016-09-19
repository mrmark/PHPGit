<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Apply a series of patches from a mailbox - `git am`.
 */
class AmCommand extends Command
{
    /**
     * Apply a series of patches from a mailbox (AKA patches).
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->am('file.patch');
     * ```
     *
     * ##### Options
     *
     * - **directory** (_string_) Prepend this to all file names
     *
     * @param string|array|\Traversable $file    Mailbox files or directories or more likely, a formatted patch file
     * @param array                     $options [optional] An array of options
     */
    public function __invoke($file, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('am');

        $this->addValues($builder, $options, ['directory']);

        if (!is_array($file) && !($file instanceof \Traversable)) {
            $file = [$file];
        }

        foreach ($file as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'directory' => false,
        ]);
    }
}

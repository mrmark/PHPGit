<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Remove files from the working tree and from the index - `git rm`.
 *
 * @author Kazuyuki Hayashi
 */
class RmCommand extends Command
{
    /**
     * @see \PHPGit\Git::rm()
     *
     * @param string|array|\Traversable $file    Files to remove. Fileglobs (e.g.  *.c) can be given to remove all matching files
     * @param array                     $options [optional] An array of options
     */
    public function __invoke($file, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('rm');

        $this->addFlags($builder, $options, ['force', 'cached']);

        if ($options['recursive']) {
            $builder->add('-r');
        }

        if (!is_array($file) && !($file instanceof \Traversable)) {
            $file = [$file];
        }

        foreach ($file as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Equivalent to $git->rm($file, ['cached' => true]);.
     *
     * ##### Options
     *
     * - **force**     (_boolean_) Override the up-to-date check
     * - **recursive** (_boolean_) Allow recursive removal when a leading directory name is given
     *
     * @param string|array|\Traversable $file    Files to remove. Fileglobs (e.g.  *.c) can be given to remove all matching files
     * @param array                     $options [optional] An array of options
     */
    public function cached($file, array $options = [])
    {
        $options['cached'] = true;

        $this->__invoke($file, $options);
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'force'     => false,
            'cached'    => false,
            'recursive' => false,
        ]);
    }
}

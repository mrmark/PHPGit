<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Move or rename a file, a directory, or a symlink - `git mv`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class MvCommand extends Command
{
    /**
     * @see \PHPGit\Git::mv()
     *
     * @param string|array|\Iterator $source      The files to move
     * @param string                 $destination The destination
     * @param array                  $options     An array of options
     */
    public function __invoke($source, $destination, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('mv');

        $this->addFlags($builder, $options, ['force']);

        if (!is_array($source) && !($source instanceof \Traversable)) {
            $source = [$source];
        }

        foreach ($source as $value) {
            $builder->add($value);
        }

        $builder->add($destination);

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'force' => false,
        ]);
    }
}

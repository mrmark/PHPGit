<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Create an archive of files from a named tree - `git archive`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class ArchiveCommand extends Command
{
    /**
     * @see \PHPGit\Git::archive()
     *
     * @param string                    $file    The filename
     * @param string                    $tree    [optional] The tree or commit to produce an archive for
     * @param string|array|\Traversable $path    [optional] If one or more paths are specified, only these are included
     * @param array                     $options [optional] An array of options
     */
    public function __invoke($file, $tree = null, $path = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('archive');

        if ($options['format']) {
            $builder->add('--format='.$options['format']);
        }

        if ($options['prefix']) {
            $builder->add('--prefix='.$options['prefix']);
        }

        $builder->add('-o')->add($file);

        if ($tree) {
            $builder->add($tree);
        }

        if (!is_array($path) && !($path instanceof \Traversable)) {
            $path = [$path];
        }

        foreach ($path as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format' => null,
            'prefix' => null,
        ]);

        $resolver->setAllowedTypes('format', ['null', 'string'])
            ->setAllowedTypes('prefix', ['null', 'string'])
            ->setAllowedValues('format', ['tar', 'zip']);
    }
}

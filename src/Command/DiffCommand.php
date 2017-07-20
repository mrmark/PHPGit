<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiffCommand extends Command
{
    /**
     * @see \PHPGit\Git::diff()
     *
     * @param string $commit  Commit or commit range to diff, EG: 'A..B' or 'A' or 'A B", etc
     * @param array  $paths   Restrict diff to file paths
     * @param array  $options An array of options
     *
     * @return string
     */
    public function __invoke($commit = null, array $paths = [], array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('diff');

        $this->addFlags($builder, $options);

        if ($options['diff-filter']) {
            $builder->add('--diff-filter='.$options['diff-filter']);
        }
        if ($commit) {
            $builder->add($commit);
        }
        if (!empty($paths)) {
            $builder->add('--');
            foreach ($paths as $path) {
                $builder->add($path);
            }
        }

        return $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'stat'        => false,
            'shortstat'   => false,
            'cached'      => false,
            'diff-filter' => null,
        ]);
    }
}

<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Show various types of objects - `git show`.
 *
 * @author Kazuyuki Hayashi
 */
class ShowCommand extends Command
{
    /**
     * @see \PHPGit\Git::show()
     *
     * @param string $object  The names of objects to show
     * @param array  $options An array of options
     *
     * @return string
     */
    public function __invoke($object, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('show');

        $this->addFlags($builder, $options, ['abbrev-commit']);

        if ($options['format']) {
            $builder->add('--format='.$options['format']);
        }

        $builder->add($object);

        return $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'format'        => null,
            'abbrev-commit' => false,
        ]);

        $resolver->setAllowedTypes('format', ['null', 'string']);
    }
}

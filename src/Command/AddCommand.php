<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Add file contents to the index - `git add`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class AddCommand extends Command
{
    /**
     * @see \PHPGit\Git::add()
     *
     * @param string|array|\Traversable $file    Files to add content from
     * @param array                     $options [optional] An array of options
     */
    public function __invoke($file, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('add');

        $this->addFlags($builder, $options);

        if (!is_array($file) && !($file instanceof \Traversable)) {
            $file = [$file];
        }

        foreach ($file as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'dry-run'        => false,
            'force'         => false,
            'ignore-errors' => false,
            'all'           => false,
            //'ignore-missing' => false,
        ]);
    }
}

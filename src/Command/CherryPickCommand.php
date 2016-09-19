<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CherryPickCommand extends Command
{
    /**
     * @see \PHPGit\Git::cherryPick()
     *
     * @param string $commit  The commit to pick
     * @param array  $options [optional] An array of options
     */
    public function __invoke($commit, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('cherry-pick');

        if ($options['x']) {
            $builder->add('-x');
        }

        $builder->add($commit);
        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'x' => false,
        ]);
    }
}

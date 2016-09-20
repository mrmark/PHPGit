<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Show the most recent tag that is reachable from a commit - `git describe`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class DescribeCommand extends Command
{
    /**
     * @see \PHPGit\Git::describe()
     *
     * @param string $committish Committish object names to describe
     * @param array  $options    An array of options
     *
     * @return string
     */
    public function __invoke($committish = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('describe');

        $this->addFlags($builder, $options, []);

        if ($committish) {
            $builder->add($committish);
        }

        return trim($this->git->run($builder->getProcess()));
    }

    /**
     * Equivalent to $git->describe($committish, ['tags' => true]);.
     *
     * @param string $committish Committish object names to describe
     * @param array  $options    An array of options
     *
     * @return string
     */
    public function tags($committish = null, array $options = [])
    {
        $options['tags'] = true;

        return $this->__invoke($committish, $options);
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'all'    => false,
            'tags'   => false,
            'always' => false,
        ]);
    }
}

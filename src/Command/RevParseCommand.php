<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Exception\GitException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Pick out and massage parameters - `git rev-parse`.
 */
class RevParseCommand extends Command
{
    /**
     * @see \PHPGit\Git::revParse()
     *
     * @param string|array|\Traversable $args    Flags and parameters to be parsed
     * @param array                     $options [optional] An array of options
     *
     * @return array
     */
    public function __invoke($args, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('rev-parse');

        $this->addFlags($builder, $options, ['abbrev-ref']);

        if ($options['short'] === true) {
            $builder->add('--short');
        } elseif ($options['short']) {
            $builder->add('--short='.$options['short']);
        }

        if (!is_array($args) && !($args instanceof \Traversable)) {
            $args = [$args];
        }

        foreach ($args as $arg) {
            $builder->add($arg);
        }

        $output = $this->git->run($builder->getProcess());

        return $this->split($output);
    }

    /**
     * Helper method to get current branch name.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $branch = $git->revParse->currentBranch();
     * ```
     *
     * ##### Output Example
     *
     * ```
     * 'master'
     * ```
     *
     * @return string
     */
    public function currentBranch()
    {
        return $this->__invoke('HEAD', ['abbrev-ref' => true])[0];
    }

    /**
     * Helper method to determine if an argument is a valid git revision.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $result = $git->revParse->isRev('master');
     * ```
     *
     * @param string $arg The value to test
     *
     * @return bool
     */
    public function isRev($arg)
    {
        try {
            $this->__invoke($arg);
        } catch (GitException $e) {
            return false;
        }

        return true;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'abbrev-ref' => false,
            'short'      => false,
        ]);
    }
}

<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Model\Log;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Show commit logs - `git log`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class LogCommand extends Command
{
    /**
     * @see \PHPGit\Git::log()
     *
     * @param string $revRange Show only commits in the specified revision range
     * @param string $path     Show only commits that are enough to explain how the files that match the specified paths came to be
     * @param array  $options  An array of options
     *
     * @return Log[]
     */
    public function __invoke($revRange = '', $path = null, array $options = [])
    {
        $commits = [];
        $options = $this->resolve($options);

        $builder = $this->git->getProcessBuilder()
            ->add('log')
            ->add('-n')->add($options['limit'])
            ->add('--skip='.$options['skip'])
            ->add('--format=%H||%aN||%aE||%aD||%s');

        $this->addFlags($builder, $options, ['extended-regexp', 'no-merges', 'reverse']);

        if ($options['grep']) {
            $builder->add('--grep='.$options['grep']);
        }

        if ($revRange) {
            $builder->add($revRange);
        }

        if ($path) {
            $builder->add('--')->add($path);
        }

        $output = $this->git->run($builder->getProcess());
        $lines  = $this->split($output);

        foreach ($lines as $line) {
            list($hash, $name, $email, $date, $title) = preg_split('/\|\|/', $line, -1, PREG_SPLIT_NO_EMPTY);

            $commits[] = new Log($name, $email, $date, $title, $hash);
        }

        return $commits;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'limit'           => 1000,
            'skip'            => 0,
            'grep'            => null,
            'extended-regexp' => false,
            'no-merges'       => false,
            'reverse'         => false,
        ]);
    }
}

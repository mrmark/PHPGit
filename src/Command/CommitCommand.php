<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Record changes to the repository - `git commit`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class CommitCommand extends Command
{
    /**
     * @see \PHPGit\Git::commit()
     *
     * @param string $message Use the given <$msg> as the commit message
     * @param array  $options An array of options
     */
    public function __invoke($message, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('commit')
            ->add('-m')->add($message);

        $this->addFlags($builder, $options, ['all', 'amend']);
        $this->addValues($builder, $options, ['reuse-message', 'squash', 'author', 'date', 'cleanup']);

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'all'           => false,
            'reuse-message' => null,
            'squash'        => null,
            'author'        => null,
            'date'          => null,
            'cleanup'       => null,
            'amend'         => false,
        ]);

        $resolver->setAllowedValues('cleanup', [null, 'default', 'verbatim', 'whitespace', 'strip']);
    }
}

<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Download objects and refs from another repository - `git fetch`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class FetchCommand extends Command
{
    /**
     * @see \PHPGit\Git::fetch()
     *
     * @param string $repository The "remote" repository that is the source of a fetch or pull operation
     * @param string $refspec    The format of a <refspec> parameter is an optional plus +, followed by the source ref <src>,
     *                           followed by a colon :, followed by the destination ref <dst>
     * @param array  $options    An array of options
     */
    public function __invoke($repository, $refspec = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('fetch');

        $this->addFlags($builder, $options);
        $builder->add($repository);

        if ($refspec) {
            $builder->add($refspec);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Fetch all remotes.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'git://your/repo.git');
     * $git->remote->add('release', 'git://your/another_repo.git');
     * $git->fetch->all();
     * ```
     *
     * Options:
     * - append (boolean) Append ref names and object names of fetched refs to the existing contents of .git/FETCH_HEAD
     * - keep   (boolean) Keep downloaded pack
     * - prune  (boolean) After fetching, remove any remote-tracking branches which no longer exist on the remote
     *
     * @param array $options An array of options
     */
    public function all(array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('fetch')
            ->add('--all');

        $this->addFlags($builder, $options);

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'append' => false,
            //'force'  => false,
            'keep'  => false,
            'prune' => false,
        ]);
    }
}

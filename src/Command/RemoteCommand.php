<?php

namespace PHPGit\Command;

use PHPGit\Command;
use PHPGit\Command\Remote\SetBranchesCommand;
use PHPGit\Command\Remote\SetHeadCommand;
use PHPGit\Command\Remote\SetUrlCommand;
use PHPGit\Git;
use PHPGit\Model\Remote;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manage set of tracked repositories - `git remote`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 *
 * @method head(string $name, string $branch = null) Sets the default branch for the named remote
 * @method branches(string $name, array $branches) Changes the list of branches tracked by the named remote
 * @method url(string $name, string $newUrl, string $oldUrl = null, array $options = []) Sets the URL remote to $newUrl
 */
class RemoteCommand extends Command
{
    /** @var SetHeadCommand */
    public $head;

    /** @var SetBranchesCommand */
    public $branches;

    /** @var SetUrlCommand */
    public $url;

    /**
     * @param Git $git
     */
    public function __construct(Git $git)
    {
        parent::__construct($git);

        $this->head     = new SetHeadCommand($git);
        $this->branches = new SetBranchesCommand($git);
        $this->url      = new SetUrlCommand($git);
    }

    /**
     * Calls sub-commands.
     *
     * @param string $name      The name of a property
     * @param array  $arguments An array of arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this->{$name}) && is_callable($this->{$name})) {
            return call_user_func_array($this->{$name}, $arguments);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', __CLASS__, $name));
    }

    /**
     * @see \PHPGit\Git::remote()
     *
     * @return Remote[]
     */
    public function __invoke()
    {
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('-v');

        $remotes = [];
        $output  = $this->git->run($builder->getProcess());
        $lines   = $this->split($output);

        foreach ($lines as $line) {
            if (preg_match('/^(.*)\t(.*)\s\((.*)\)$/', $line, $matches)) {
                if (!isset($remotes[$matches[1]])) {
                    $remotes[$matches[1]] = [];
                }

                $remotes[$matches[1]][$matches[3]] = $matches[2];
            }
        }

        // Remap the array data to the Remote model.
        foreach ($remotes as $name => $urls) {
            $remote = new Remote($name);

            if (array_key_exists('fetch', $urls)) {
                $remote->fetch = $urls['fetch'];
            }
            if (array_key_exists('push', $urls)) {
                $remote->push = $urls['push'];
            }

            $remotes[$name] = $remote;
        }

        return $remotes;
    }

    /**
     * Adds a remote named $name for the repository at $url.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->fetch('origin');
     * ```
     *
     * Options:
     * - tags    (boolean) With this option, `git fetch <name>` imports every tag from the remote repository
     * - no-tags (boolean) With this option, `git fetch <name>` does not import tags from the remote repository
     *
     * @param string $name    The name of the remote
     * @param string $url     The url of the remote
     * @param array  $options An array of options
     */
    public function add($name, $url, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('add');

        $this->addFlags($builder, $options, ['tags', 'no-tags']);

        $builder->add($name)->add($url);

        $this->git->run($builder->getProcess());
    }

    /**
     * Rename the remote named $name to $newName.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->rename('origin', 'upstream');
     * ```
     *
     * @param string $name    The remote name to rename
     * @param string $newName The new remote name
     */
    public function rename($name, $newName)
    {
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('rename')
            ->add($name)
            ->add($newName);

        $this->git->run($builder->getProcess());
    }

    /**
     * Remove the remote named $name.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->rm('origin');
     * ```
     *
     * @param string $name The remote name to remove
     */
    public function rm($name)
    {
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('rm')
            ->add($name);

        $this->git->run($builder->getProcess());
    }

    /**
     * Gives some information about the remote $name.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->clone('https://github.com/kzykhys/Text.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * echo $git->remote->show('origin');
     * ```
     *
     * Output Example:
     *
     * ```
     * \* remote origin
     *   Fetch URL: https://github.com/kzykhys/Text.git
     *   Push  URL: https://github.com/kzykhys/Text.git
     *   HEAD branch: master
     *   Remote branch:
     *     master tracked
     *   Local branch configured for 'git pull':
     *     master merges with remote master
     *   Local ref configured for 'git push':
     *     master pushes to master (up to date)
     * ```
     *
     * @param string $name The remote name to show
     *
     * @return string
     */
    public function show($name)
    {
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('show')
            ->add($name);

        return $this->git->run($builder->getProcess());
    }

    /**
     * Deletes all stale remote-tracking branches under $name.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->prune('origin');
     * ```
     *
     * @param string $name The remote name
     */
    public function prune($name = null)
    {
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('prune');

        if ($name) {
            $builder->add($name);
        }

        $this->git->run($builder->getProcess());
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tags'    => false,
            'no-tags' => false,
        ]);
    }
}

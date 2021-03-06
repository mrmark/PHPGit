<?php

namespace PHPGit\Command\Remote;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Changes URL remote points to.
 *
 * @author Kazuyuki Hayashi
 */
class SetUrlCommand extends Command
{
    /**
     * Alias of set().
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->url('origin', 'https://github.com/text/Text.git');
     * ```
     *
     * Options:
     * - push (boolean) Push URLs are manipulated instead of fetch URLs
     *
     * @param string $name    The name of remote
     * @param string $newUrl  The new URL
     * @param string $oldUrl  The old URL
     * @param array  $options An array of options
     */
    public function __invoke($name, $newUrl, $oldUrl = null, array $options = [])
    {
        $this->set($name, $newUrl, $oldUrl, $options);
    }

    /**
     * Sets the URL remote to $newUrl.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->url->set('origin', 'https://github.com/text/Text.git');
     * ```
     *
     * Options:
     * - push (boolean) Push URLs are manipulated instead of fetch URLs
     *
     * @param string $name    The name of remote
     * @param string $newUrl  The new URL
     * @param string $oldUrl  The old URL
     * @param array  $options An array of options
     */
    public function set($name, $newUrl, $oldUrl = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('set-url');

        $this->addFlags($builder, $options);

        $builder
            ->add($name)
            ->add($newUrl);

        if ($oldUrl) {
            $builder->add($oldUrl);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Adds new URL to remote.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->url->add('origin', 'https://github.com/text/Text.git');
     * ```
     *
     * Options:
     * - push (boolean) Push URLs are manipulated instead of fetch URLs
     *
     * @param string $name    The name of remote
     * @param string $newUrl  The new URL
     * @param array  $options An array of options
     */
    public function add($name, $newUrl, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('set-url')
            ->add('--add');

        $this->addFlags($builder, $options);

        $builder
            ->add($name)
            ->add($newUrl);

        $this->git->run($builder->getProcess());
    }

    /**
     * Deletes all URLs matching regex $url.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->url->delete('origin', 'https://github.com');
     * ```
     *
     * Options:
     * - push (boolean) Push URLs are manipulated instead of fetch URLs
     *
     * @param string $name    The remote name
     * @param string $url     The URL to delete
     * @param array  $options An array of options
     */
    public function delete($name, $url, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('remote')
            ->add('set-url')
            ->add('--delete');

        $this->addFlags($builder, $options);

        $builder
            ->add($name)
            ->add($url);

        $this->git->run($builder->getProcess());
    }

    /**
     * {@inheritdoc}
     *
     * - push (boolean) Push URLs are manipulated instead of fetch URLs
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'push' => false,
        ]);
    }
}

<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Create, list, delete or verify a tag object signed with GPG - `git tag`.
 *
 * @author Kazuyuki Hayashi
 */
class TagCommand extends Command
{
    /**
     * Returns an array of tags.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->clone('https://github.com/kzykhys/PHPGit.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $tags = $git->tag();
     * ```
     *
     * ##### Output Example
     *
     * ```
     * ['v1.0.0', 'v1.0.1', 'v1.0.2']
     * ```
     *
     *
     * @return array
     */
    public function __invoke()
    {
        $builder = $this->git->getProcessBuilder()
            ->add('tag');

        $output = $this->git->run($builder->getProcess());

        return $this->split($output);
    }

    /**
     * Creates a tag object.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->tag->create('v1.0.0');
     * ```
     *
     * ##### Options
     *
     * - **annotate** (_boolean_) Make an unsigned, annotated tag object
     * - **sign**     (_boolean_) Make a GPG-signed tag, using the default e-mail address’s key
     * - **force**    (_boolean_) Replace an existing tag with the given name (instead of failing)
     * - **message**  (_string_)  Tag message
     *
     * @param string $tag     The name of the tag to create
     * @param string $commit  The SHA1 object name of the commit object
     * @param array  $options [optional] An array of options {@see TagCommand::setDefaultOptions}
     */
    public function create($tag, $commit = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('tag')
            ->add($tag);

        $this->addFlags($builder, $options, ['annotate', 'sign', 'force']);

        if ($options['message']) {
            $builder->add('-m')->add($options['message']);
        }
        if ($commit) {
            $builder->add($commit);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Delete existing tags with the given names.
     *
     * @param string|array|\Traversable $tag The name of the tag to create
     */
    public function delete($tag)
    {
        $builder = $this->git->getProcessBuilder()
            ->add('tag')
            ->add('-d');

        if (!is_array($tag) && !($tag instanceof \Traversable)) {
            $tag = [$tag];
        }

        foreach ($tag as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * Verify the gpg signature of the given tag names.
     *
     * @param string|array|\Traversable $tag The name of the tag to create
     */
    public function verify($tag)
    {
        $builder = $this->git->getProcessBuilder()
            ->add('tag')
            ->add('-v');

        if (!is_array($tag) && !($tag instanceof \Traversable)) {
            $tag = [$tag];
        }

        foreach ($tag as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());
    }

    /**
     * {@inheritdoc}
     *
     * - **annotate** (_boolean_) Make an unsigned, annotated tag object
     * - **sign**     (_boolean_) Make a GPG-signed tag, using the default e-mail address’s key
     * - **force**    (_boolean_) Replace an existing tag with the given name (instead of failing)
     * - **message**  (_string_)  Tag message
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'annotate' => false,
            'sign'     => false,
            'force'    => false,
            'message'  => null,
        ]);
    }
}

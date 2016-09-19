<?php

namespace PHPGit\Command;

use PHPGit\Command;

/**
 * Summarize 'git log' output - `git shortlog`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class ShortlogCommand extends Command
{
    /**
     * @see \PHPGit\Git::shortlog()
     *
     * @param string|array|\Traversable $commits [optional] Defaults to HEAD
     *
     * @return array
     */
    public function __invoke($commits = 'HEAD')
    {
        $builder = $this->git->getProcessBuilder()
            ->add('shortlog')
            ->add('--numbered')
            ->add('--format=')
            ->add('-w256,2,2')
            ->add('-e');

        if (!is_array($commits) && !($commits instanceof \Traversable)) {
            $commits = [$commits];
        }

        foreach ($commits as $commit) {
            $builder->add($commit);
        }

        $process = $builder->getProcess();
        $process->setCommandLine(str_replace('--format=', '--format=%h|%ci|%s', $process->getCommandLine()));

        $output = $this->git->run($process);
        $lines  = $this->split($output);
        $result = [];
        $author = null;

        foreach ($lines as $line) {
            if (substr($line, 0, 1) != ' ') {
                if (preg_match('/([^<>]*? <[^<>]+>)/', $line, $matches)) {
                    $author          = $matches[1];
                    $result[$author] = [];
                }
                continue;
            }

            list($commit, $date, $subject) = explode('|', trim($line), 3);
            $result[$author][]             = [
                'commit'  => $commit,
                'date'    => new \DateTime($date),
                'subject' => $subject,
            ];
        }

        return $result;
    }

    /**
     * Suppress commit description and provide a commit count summary only.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $shortlog = $git->shortlog->summary();
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     'John Doe <john@example.com>' => 153,
     *     //...
     * ]
     * ```
     *
     * @param string $commits [optional] Defaults to HEAD
     *
     * @return array
     */
    public function summary($commits = 'HEAD')
    {
        $builder = $this->git->getProcessBuilder()
            ->add('shortlog')
            ->add('--numbered')
            ->add('--summary')
            ->add('-e');

        if (!is_array($commits) && !($commits instanceof \Traversable)) {
            $commits = [$commits];
        }

        foreach ($commits as $commit) {
            $builder->add($commit);
        }

        $output = $this->git->run($builder->getProcess());
        $lines  = $this->split($output);
        $result = [];

        foreach ($lines as $line) {
            list($commits, $author) = explode("\t", trim($line), 2);
            $result[$author]        = (int) $commits;
        }

        return $result;
    }
}

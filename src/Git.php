<?php

namespace PHPGit;

use PHPGit\Exception\GitException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * PHPGit - A Git wrapper for PHP5.3+
 * ==================================.
 *
 * [![Latest Unstable Version](https://poser.pugx.org/kzykhys/git/v/unstable.png)](https://packagist.org/packages/kzykhys/git)
 * [![Build Status](https://travis-ci.org/kzykhys/PHPGit.png?branch=master)](https://travis-ci.org/kzykhys/PHPGit)
 * [![Coverage Status](https://coveralls.io/repos/kzykhys/PHPGit/badge.png)](https://coveralls.io/r/kzykhys/PHPGit)
 * [![SensioLabsInsight](https://insight.sensiolabs.com/projects/04f10b57-a113-47ad-8dda-9a6dacbb079f/mini.png)](https://insight.sensiolabs.com/projects/04f10b57-a113-47ad-8dda-9a6dacbb079f)
 *
 * Requirements
 * ------------
 *
 * * PHP5.3
 * * Git
 *
 * Installation
 * ------------
 *
 * Update your composer.json and run `composer update`
 *
 * ``` json
 * {
 *     "require": {
 *         "kzykhys/git": "dev-master"
 *     }
 * }
 * ```
 *
 * Basic Usage
 * -----------
 *
 * ``` php
 * <?php
 *
 * require __DIR__ . '/vendor/autoload.php';
 *
 * $git = new PHPGit\Git();
 * $git->clone('https://github.com/kzykhys/PHPGit.git', '/path/to/repo');
 * $git->setRepository('/path/to/repo');
 * $git->remote->add('production', 'git://example.com/your/repo.git');
 * $git->add('README.md');
 * $git->commit('Adds README.md');
 * $git->checkout('release');
 * $git->merge('master');
 * $git->push();
 * $git->push('production', 'release');
 * $git->tag->create('v1.0.1', 'release');
 *
 * foreach ($git->tree('release') as $object) {
 *     if ($object['type'] == 'blob') {
 *         echo $git->show($object['file']);
 *     }
 * }
 * ```
 *
 * @author  Kazuyuki Hayashi <hayashi@valnur.net>
 * @license MIT
 *
 * @method bool add($file, $options = [])                                   Add file contents to the index
 * @method bool archive($file, $tree = null, $path = null, $options = [])   Create an archive of files from a named tree
 * @method array branch($options = [])                                      List both remote-tracking branches and local branches
 * @method bool checkout($branch, $options = [])                            Checkout a branch or paths to the working tree
 * @method bool cherryPick($commit, $options = [])                          Cherry-pick a commit to HEAD
 * @method bool clone($repository, $path = null, $options = [])             Clone a repository into a new directory
 * @method bool commit($message = '', $options = [])                        Record changes to the repository
 * @method array config($options = [])                                      List all variables set in config file
 * @method string describe($committish = null, $options = [])               Returns the most recent tag that is reachable from a commit
 * @method bool fetch($repository, $refspec = null, $options = [])          Fetches named heads or tags from one or more other repositories
 * @method bool init($path, $options = [])                                  Create an empty git repository or reinitialize an existing one
 * @method array log($revRange = '', $path = null, array $options = [])     Returns the commit logs
 * @method bool merge($commit, $message = null, $options = [])              Incorporates changes from the named commits into the current branch
 * @method bool mv($source, $destination, $options = [])                    Move or rename a file, a directory, or a symlink
 * @method bool pull($repository = null, $refspec = null, $options = [])    Fetch from and merge with another repository or a local branch
 * @method bool push($repository = null, $refspec = null, $options = [])    Update remote refs along with associated objects
 * @method bool rebase($upstream = null, $branch = null, $options = [])     Forward-port local commits to the updated upstream head
 * @method array remote()                                                   Returns an array of existing remotes
 * @method bool reset($commit = null, $paths = [])                          Resets the index entries for all <paths> to their state at <commit>
 * @method bool rm($file, $options = [])                                    Remove files from the working tree and from the index
 * @method array shortlog($commits = [])                                    Summarize 'git log' output
 * @method string show($object, $options = [])                              Shows one or more objects (blobs, trees, tags and commits)
 * @method bool stash()                                                     Save your local modifications to a new stash, and run git reset --hard to revert them
 * @method array status($options = [])                                      Show the working tree status
 * @method array tag()                                                      Returns an array of tags
 * @method array tree($branch = 'master', $path = '')                       List the contents of a tree object
 */
class Git
{
    /** @var Command\AddCommand */
    public $add;

    /** @var Command\ArchiveCommand */
    public $archive;

    /** @var Command\BranchCommand */
    public $branch;

    /** @var Command\CatCommand */
    public $cat;

    /** @var Command\CheckoutCommand */
    public $checkout;

    /** @var Command\CherryPickCommand */
    public $cherryPick;

    /** @var Command\CloneCommand */
    public $clone;

    /** @var Command\CommitCommand */
    public $commit;

    /** @var Command\ConfigCommand */
    public $config;

    /** @var Command\DescribeCommand */
    public $describe;

    // Not implemented yet
    public $diff;

    /** @var Command\FetchCommand */
    public $fetch;

    /** @var Command\InitCommand */
    public $init;

    /** @var Command\LogCommand */
    public $log;

    /** @var Command\MergeCommand */
    public $merge;

    /** @var Command\MvCommand */
    public $mv;

    /** @var Command\PullCommand */
    public $pull;

    /** @var Command\PushCommand */
    public $push;

    /** @var Command\RebaseCommand */
    public $rebase;

    /** @var Command\RemoteCommand */
    public $remote;

    /** @var Command\ResetCommand */
    public $reset;

    /** @var Command\RmCommand */
    public $rm;

    /** @var Command\ShortlogCommand */
    public $shortlog;

    /** @var Command\ShowCommand */
    public $show;

    /** @var Command\StashCommand */
    public $stash;

    /** @var Command\StatusCommand */
    public $status;

    /** @var Command\TagCommand */
    public $tag;

    /** @var Command\TreeCommand */
    public $tree;

    /** @var string */
    private $bin = 'git';

    /** @var string */
    private $directory = '.';

    /** @var float|null */
    private $timeout = null;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Initializes sub-commands.
     */
    public function __construct()
    {
        $this->add        = new Command\AddCommand($this);
        $this->archive    = new Command\ArchiveCommand($this);
        $this->branch     = new Command\BranchCommand($this);
        $this->cat        = new Command\CatCommand($this);
        $this->checkout   = new Command\CheckoutCommand($this);
        $this->cherryPick = new Command\CherryPickCommand($this);
        $this->clone      = new Command\CloneCommand($this);
        $this->commit     = new Command\CommitCommand($this);
        $this->config     = new Command\ConfigCommand($this);
        $this->describe   = new Command\DescribeCommand($this);
        $this->fetch      = new Command\FetchCommand($this);
        $this->init       = new Command\InitCommand($this);
        $this->log        = new Command\LogCommand($this);
        $this->merge      = new Command\MergeCommand($this);
        $this->mv         = new Command\MvCommand($this);
        $this->pull       = new Command\PullCommand($this);
        $this->push       = new Command\PushCommand($this);
        $this->rebase     = new Command\RebaseCommand($this);
        $this->remote     = new Command\RemoteCommand($this);
        $this->reset      = new Command\ResetCommand($this);
        $this->rm         = new Command\RmCommand($this);
        $this->shortlog   = new Command\ShortlogCommand($this);
        $this->show       = new Command\ShowCommand($this);
        $this->stash      = new Command\StashCommand($this);
        $this->status     = new Command\StatusCommand($this);
        $this->tag        = new Command\TagCommand($this);
        $this->tree       = new Command\TreeCommand($this);
    }

    /**
     * Calls sub-commands.
     *
     * @param string $name      The name of a property
     * @param array  $arguments An array of arguments
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this->{$name}) && is_callable($this->{$name})) {
            return call_user_func_array($this->{$name}, $arguments);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method PHPGit\Git::%s()', $name));
    }

    /**
     * Sets the Git binary path.
     *
     * @param string $bin
     *
     * @return Git
     */
    public function setBin($bin)
    {
        $this->bin = $bin;

        return $this;
    }

    /**
     * Sets the Git repository path.
     *
     * @param string $directory
     *
     * @return Git
     */
    public function setRepository($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Returns version number.
     *
     * @return mixed
     */
    public function getVersion()
    {
        $process = $this->getProcessBuilder()
            ->add('--version')
            ->getProcess();

        return $this->run($process);
    }

    /**
     * Set git command timeout.
     *
     * @param float|null $timeout
     *
     * @return Git
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Returns git command timeout.
     *
     * @return float|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the logger.
     *
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Returns the logger.
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Determine if a logger is available.
     *
     * @return bool
     */
    public function hasLogger()
    {
        return $this->logger instanceof LoggerInterface;
    }

    /**
     * Returns an instance of ProcessBuilder.
     *
     * @return ProcessBuilder
     */
    public function getProcessBuilder()
    {
        return ProcessBuilder::create()
            ->setPrefix($this->bin)
            ->setTimeout($this->timeout)
            ->setWorkingDirectory($this->directory);
    }

    /**
     * Executes a process.
     *
     * @param Process $process The process to run
     *
     * @throws Exception\GitException
     *
     * @return mixed
     */
    public function run(Process $process)
    {
        if ($this->hasLogger()) {
            $this->logger->info(sprintf('RUN  %s ', $process->getCommandLine()));
        }

        $process->run();

        if (!$process->isSuccessful()) {
            throw new GitException($process->getErrorOutput(), $process->getExitCode(), $process->getCommandLine());
        }

        $output = $process->getOutput();

        if ($this->hasLogger()) {
            $this->logger->debug(sprintf('OUT  %s', $output));
            $this->logger->info('RES  Command ran successfully');
        }

        return $output;
    }
}

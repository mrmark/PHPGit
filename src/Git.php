<?php

namespace PHPGit;

use PHPGit\Exception\GitException;
use PHPGit\Model\Branch;
use PHPGit\Model\Log;
use PHPGit\Model\Remote;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * PHPGit - A Git wrapper for PHP5.3+.
 *
 * Basic Usage
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
 * @method clone(string $repository, string $path = null, array $options = []) Clone a repository into a new directory
 *
 * @todo Create clone method once we are on PHP7
 */
class Git
{
    /** @var Command\AddCommand */
    private $add;

    /** @var Command\AmCommand */
    private $am;

    /** @var Command\ArchiveCommand */
    private $archive;

    /** @var Command\BranchCommand */
    public $branch;

    /** @var Command\CatCommand */
    public $cat;

    /** @var Command\CheckoutCommand */
    public $checkout;

    /** @var Command\CherryPickCommand */
    private $cherryPick;

    /**
     * @var Command\CloneCommand
     *
     * @todo When clone is moved to method, then change this to private
     */
    public $clone;

    /** @var Command\CommitCommand */
    private $commit;

    /** @var Command\ConfigCommand */
    public $config;

    /** @var Command\DescribeCommand */
    public $describe;

    /** @var Command\DiffCommand */
    private $diff;

    /** @var Command\FetchCommand */
    public $fetch;

    /** @var Command\InitCommand */
    private $init;

    /** @var Command\LogCommand */
    private $log;

    /** @var Command\MergeCommand */
    public $merge;

    /** @var Command\MvCommand */
    private $mv;

    /** @var Command\PullCommand */
    private $pull;

    /** @var Command\PushCommand */
    private $push;

    /** @var Command\RebaseCommand */
    public $rebase;

    /** @var Command\RemoteCommand */
    public $remote;

    /** @var Command\ResetCommand */
    public $reset;

    /** @var Command\RevParseCommand */
    public $revParse;

    /** @var Command\RmCommand */
    public $rm;

    /** @var Command\ShortlogCommand */
    public $shortlog;

    /** @var Command\ShowCommand */
    private $show;

    /** @var Command\StashCommand */
    public $stash;

    /** @var Command\StatusCommand */
    private $status;

    /** @var Command\TagCommand */
    public $tag;

    /** @var Command\TreeCommand */
    private $tree;

    /** @var string */
    private $bin = 'git';

    /** @var string|null */
    private $directory = null;

    /** @var float|null */
    private $timeout = null;

    /** @var null|callable */
    private $runCallback = null;

    /**
     * Initializes sub-commands.
     */
    public function __construct()
    {
        $this->add        = new Command\AddCommand($this);
        $this->am         = new Command\AmCommand($this);
        $this->archive    = new Command\ArchiveCommand($this);
        $this->branch     = new Command\BranchCommand($this);
        $this->cat        = new Command\CatCommand($this);
        $this->checkout   = new Command\CheckoutCommand($this);
        $this->cherryPick = new Command\CherryPickCommand($this);
        $this->clone      = new Command\CloneCommand($this);
        $this->commit     = new Command\CommitCommand($this);
        $this->config     = new Command\ConfigCommand($this);
        $this->describe   = new Command\DescribeCommand($this);
        $this->diff       = new Command\DiffCommand($this);
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
        $this->revParse   = new Command\RevParseCommand($this);
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
     * Delegate process running to a callback.
     *
     * The callback should expect one parameter of type Process.
     *
     * @param callable|null $callback
     *
     * @return Git
     */
    public function setRunCallback(callable $callback = null)
    {
        $this->runCallback = $callback;

        return $this;
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
     * @return string
     */
    public function run(Process $process)
    {
        if (is_callable($this->runCallback)) {
            call_user_func($this->runCallback, $process);
        } else {
            $process->run();
        }

        if (!$process->isSuccessful()) {
            throw new GitException($process);
        }

        return $process->getOutput();
    }

    /**
     * Add file contents to the index.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->add('file.txt');
     * $git->add('file.txt', ['force' => false, 'ignore-errors' => false);
     * ```
     *
     * Options:
     * - force          (boolean) Allow adding otherwise ignored files
     * - ignore-errors  (boolean) Do not abort the operation
     * - all            (boolean) This adds, modifies, and removes index entries to match the working tree
     *
     * @param string|array|\Traversable $file    Files to add content from
     * @param array                     $options An array of options
     */
    public function add($file, array $options = [])
    {
        $this->add->__invoke($file, $options);
    }

    /**
     * Apply a series of patches from a mailbox (AKA patches).
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->am('file.patch');
     * ```
     *
     * Options:
     * - directory (string) Prepend this to all file names
     *
     * @param string|array|\Traversable $file    Mailbox files or directories or more likely, a formatted patch file
     * @param array                     $options An array of options
     */
    public function am($file, array $options = [])
    {
        $this->am->__invoke($file, $options);
    }

    /**
     * Create an archive of files from a named tree.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->archive('repo.zip', 'master', null, ['format' => 'zip']);
     * ```
     *
     * Options:
     * - format (boolean) Format of the resulting archive: tar or zip
     * - prefix (boolean) Prepend prefix/ to each filename in the archive
     *
     * @param string                    $file    The filename
     * @param string                    $tree    The tree or commit to produce an archive for
     * @param string|array|\Traversable $path    If one or more paths are specified, only these are included
     * @param array                     $options An array of options
     */
    public function archive($file, $tree = null, $path = null, array $options = [])
    {
        $this->archive->__invoke($file, $tree, $path, $options);
    }

    /**
     * Returns an array of both remote-tracking branches and local branches.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $branches = $git->branch();
     * ```
     *
     * Output Example:
     *
     * ```
     * [
     *     'master' => new Branch['current' => true, 'name' => 'master', 'hash' => 'bf231bb', 'title' => 'Initial Commit'],
     *     'origin/master' => new Branch['current' => false, 'name' => 'origin/master', 'alias' => 'remotes/origin/master']
     * ]
     * ```
     *
     * Options:
     * - all     (boolean)        List both remote-tracking branches and local branches
     * - remotes (boolean)        List the remote-tracking branches
     * - merged  (boolean|string) Only list branches whose tips are reachable from the specified commit (HEAD if not specified)
     *
     * @param array $options An array of options
     *
     * @return Branch[]
     */
    public function branch(array $options = [])
    {
        return $this->branch->__invoke($options);
    }

    /**
     * Switches branches by updating the index, working tree, and HEAD to reflect the specified branch or commit.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->checkout('develop');
     * ```
     *
     * Options:
     * - force (boolean) Proceed even if the index or the working tree differs from HEAD
     * - merge (boolean) Merges local modification
     *
     * @param string $branch  Branch to checkout
     * @param array  $options An array of options
     */
    public function checkout($branch, array $options = [])
    {
        $this->checkout->__invoke($branch, $options);
    }

    /**
     * Given existing commit, apply the change it introduces, recording a new commit.
     * This requires your working tree to be clean (no modifications from the HEAD commit).
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->cherryPick('abc123');
     * ```
     *
     * Options:
     * - x (boolean) When recording the commit, append a line that says "(cherry picked from commit ...)" to the original commit message
     *
     * @param string $commit  The commit to pick
     * @param array  $options An array of options
     */
    public function cherryPick($commit, array $options = [])
    {
        $this->cherryPick->__invoke($commit, $options);
    }

    /**
     * Record changes to the repository.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->clone('https://github.com/kzykhys/PHPGit.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $git->add('README.md');
     * $git->commit('Fixes README.md');
     * ```
     *
     * Options:
     * - all           (boolean) Stage files that have been modified and deleted
     * - reuse-message (string)  Take an existing commit object, and reuse the log message and the authorship information (including the timestamp) when creating the commit
     * - squash        (string)  Construct a commit message for use with rebase --autosquash
     * - author        (string)  Override the commit author
     * - date          (string)  Override the author date used in the commit
     * - cleanup       (string)  Can be one of verbatim, whitespace, strip, and default
     * - amend         (boolean) Used to amend the tip of the current branch
     *
     * @param string $message Use the given <$msg> as the commit message
     * @param array  $options An array of options
     */
    public function commit($message, array $options = [])
    {
        $this->commit->__invoke($message, $options);
    }

    /**
     * Returns all variables set in config file.
     *
     * Options:
     * - global (boolean) Read or write configuration options for the current user
     * - system (boolean) Read or write configuration options for all users on the current machine
     *
     * @param array $options An array of options
     *
     * @return array
     */
    public function config(array $options = [])
    {
        return $this->config->__invoke($options);
    }

    /**
     * Returns the most recent tag that is reachable from a commit.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->tag->create('v1.0.0');
     * $git->commit('Fixes #14');
     * echo $git->describe('HEAD', ['tags' => true]);
     * ```
     *
     * Output Example:
     *
     * ```
     * v1.0.0-1-g7049efc
     * ```
     *
     * Options:
     * - all    (boolean) Enables matching any known branch, remote-tracking branch, or lightweight tag
     * - tags   (boolean) Enables matching a lightweight (non-annotated) tag
     * - always (boolean) Show uniquely abbreviated commit object as fallback
     *
     * @param string $committish Committish object names to describe
     * @param array  $options    An array of options
     *
     * @return string
     */
    public function describe($committish = null, array $options = [])
    {
        return $this->describe->__invoke($committish, $options);
    }

    /**
     * Show changes between commits, commit and working tree, etc.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $output = $git->diff('A..B');
     * ```
     *
     * Options:
     * - stat      (boolean) Generate a diff stat
     * - shortstat (boolean) Output only the last line of the --stat format containing total number of modified files, as well as number of added and deleted lines
     * - cached    (boolean) Work on files staged in the index
     *
     * @param string $commit  Commit or commit range to diff, EG: 'A..B' or 'A' or 'A B", etc
     * @param string $path    Restrict diff to file path
     * @param array  $options An array of options
     *
     * @return string
     */
    public function diff($commit = null, $path = null, array $options = [])
    {
        return $this->diff->__invoke($commit, $path, $options);
    }

    /**
     * Fetches named heads or tags from one or more other repositories, along with the objects necessary to complete them.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'git://your/repo.git');
     * $git->fetch('origin');
     * ```
     *
     * Options:
     * - append (boolean) Append ref names and object names of fetched refs to the existing contents of .git/FETCH_HEAD
     * - keep   (boolean) Keep downloaded pack
     * - prune  (boolean) After fetching, remove any remote-tracking branches which no longer exist on the remote
     *
     * @param string $repository The "remote" repository that is the source of a fetch or pull operation
     * @param string $refspec    The format of a <refspec> parameter is an optional plus +, followed by the source ref <src>,
     *                           followed by a colon :, followed by the destination ref <dst>
     * @param array  $options    An array of options
     */
    public function fetch($repository, $refspec = null, array $options = [])
    {
        $this->fetch->__invoke($repository, $refspec, $options);
    }

    /**
     * Create an empty git repository or reinitialize an existing one.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->init('/path/to/repo1');
     * $git->init('/path/to/repo2', array('shared' => true, 'bare' => true));
     * ```
     *
     * Options:
     * - shared (boolean) Specify that the git repository is to be shared amongst several users
     * - bare   (boolean) Create a bare repository
     *
     * @param string $path    The directory to create an empty repository
     * @param array  $options An array of options
     */
    public function init($path, array $options = [])
    {
        $this->init->__invoke($path, $options);
    }

    /**
     * Returns the commit logs.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $logs = $git->log(array('limit' => 10));
     * ```
     *
     * Output Example:
     *
     * ``` php
     * [
     *     0 => new Log[
     *         'hash'  => '1a821f3f8483747fd045eb1f5a31c3cc3063b02b',
     *         'name'  => 'John Doe',
     *         'email' => 'john@example.com',
     *         'date'  => 'Fri Jan 17 16:32:49 2014 +0900',
     *         'title' => 'Initial Commit'
     *     ],
     *     1 => new Log(),
     * ]
     * ```
     *
     * Options:
     * - limit            (integer) Limits the number of commits to show
     * - skip             (integer) Skip number commits before starting to show the commit output
     * - grep             (string) Limit the commits output to ones with log message that matches the specified pattern (regular expression)
     * - extended-regexp  (bool)    Consider the limiting patterns to be extended regular expressions instead of the default basic regular expressions
     * - no-merges        (bool)    Consider the limiting patterns to be extended regular expressions instead of the default basic regular expressions
     * - reverse          (bool)    Reverse the order of the commits
     *
     * @param string $revRange Show only commits in the specified revision range
     * @param string $path     Show only commits that are enough to explain how the files that match the specified paths came to be
     * @param array  $options  An array of options
     *
     * @return Log[]
     */
    public function log($revRange = '', $path = null, array $options = [])
    {
        return $this->log->__invoke($revRange, $path, $options);
    }

    /**
     * Incorporates changes from the named commits into the current branch.
     *
     * ```php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->merge('1.0');
     * $git->merge('1.1', 'Merge message', ['strategy' => 'ours']);
     * ```
     *
     * Options:
     * - no-ff               (boolean) Create a merge commit even when the merge resolves as a fast-forward
     * - ff-only             (boolean) Refuse to merge and exit with a non-zero status unless the current HEAD is already up-to-date or the merge can be resolved as a fast-forward
     * - rerere-autoupdate   (boolean) Allow the rerere mechanism to update the index with the result of auto-conflict resolution if possible
     * - squash              (boolean) Allows you to create a single commit on top of the current branch whose effect is the same as merging another branch
     * - strategy            (string)  Use the given merge strategy
     * - strategy-option     (string)  Pass merge strategy specific option through to the merge strategy
     *
     * @param string|array|\Traversable $commit  Commits to merge into our branch
     * @param string                    $message Commit message to be used for the merge commit
     * @param array                     $options An array of options
     */
    public function merge($commit, $message = null, array $options = [])
    {
        $this->merge->__invoke($commit, $message, $options);
    }

    /**
     * Move or rename a file, a directory, or a symlink.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->mv('UPGRADE-1.0.md', 'UPGRADE-1.1.md');
     * ```
     *
     * Options:
     * - force (boolean) Force renaming or moving of a file even if the target exists
     *
     * @param string|array|\Iterator $source      The files to move
     * @param string                 $destination The destination
     * @param array                  $options     An array of options
     */
    public function mv($source, $destination, array $options = [])
    {
        $this->mv->__invoke($source, $destination, $options);
    }

    /**
     * Fetch from and merge with another repository or a local branch.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->pull('origin', 'master');
     * ```
     *
     * @param string $repository The "remote" repository that is the source of a fetch or pull operation
     * @param string $refspec    The format of a <refspec> parameter is an optional plus +,
     *                           followed by the source ref <src>, followed by a colon :, followed by the destination ref <dst>
     */
    public function pull($repository = null, $refspec = null)
    {
        $this->pull->__invoke($repository, $refspec);
    }

    /**
     * Update remote refs along with associated objects.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->push('origin', 'master');
     * ```
     *
     * Options:
     * - all    (boolean) Push all branches
     * - mirror (boolean) All refs under refs/ be mirrored to the remote repository
     * - tags   (boolean)
     * - force  (boolean)
     *
     * @param string $repository The "remote" repository that is destination of a push operation
     * @param string $refspec    Specify what destination ref to update with what source object
     * @param array  $options    An array of options
     */
    public function push($repository = null, $refspec = null, array $options = [])
    {
        $this->push->__invoke($repository, $refspec, $options);
    }

    /**
     * Forward-port local commits to the updated upstream head.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->fetch('origin');
     * $git->rebase('origin/master');
     * ```
     *
     * Options:
     * - onto          (string)  Starting point at which to create the new commits
     * - no-verify     (boolean) Bypasses the pre-rebase hook
     * - force-rebase  (boolean) Force the rebase even if the current branch is a descendant of the commit you are rebasing onto
     *
     * @param string $upstream Upstream branch to compare against
     * @param string $branch   Working branch; defaults to HEAD
     * @param array  $options  An array of options
     */
    public function rebase($upstream = null, $branch = null, array $options = [])
    {
        $this->rebase->__invoke($upstream, $branch, $options);
    }

    /**
     * Returns an array of existing remotes.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->clone('https://github.com/kzykhys/Text.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $remotes = $git->remote();
     * ```
     *
     * Output Example:
     *
     * ``` php
     * [
     *     'origin' => new Remote[
     *         'name'  => 'origin',
     *         'fetch' => 'https://github.com/kzykhys/Text.git',
     *         'push'  => 'https://github.com/kzykhys/Text.git'
     *     ]
     * ]
     * ```
     *
     * @return Remote[]
     */
    public function remote()
    {
        return $this->remote->__invoke();
    }

    /**
     * Resets the index entries for all $paths to their state at $commit.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->reset();
     * ```
     *
     * @param string|array|\Traversable $paths  The paths to reset
     * @param string                    $commit The commit
     */
    public function reset($paths, $commit = null)
    {
        $this->reset->__invoke($paths, $commit);
    }

    /**
     * Pick out and massage parameters.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->revParse();
     * ```
     *
     * Options:
     * - abbrev-ref (string)   A non-ambiguous short name of the objects name (AKA branch name)
     * - short      (int|bool) Instead of outputting the full SHA-1 values of object names try to abbreviate
     *                               them to a shorter unique name. When true, 7 or shorter is used. The minimum length is 4.
     *
     * @param string|array|\Traversable $args    Flags and parameters to be parsed
     * @param array                     $options An array of options
     *
     * @return array
     */
    public function revParse($args, array $options = [])
    {
        return $this->revParse->__invoke($args, $options);
    }

    /**
     * Remove files from the working tree and from the index.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->rm('CHANGELOG-1.0-1.1.txt', ['force' => true]);
     * ```
     *
     * Options:
     * - force     (boolean) Override the up-to-date check
     * - cached    (boolean) Unstage and remove paths only from the index
     * - recursive (boolean) Allow recursive removal when a leading directory name is given
     *
     * @param string|array|\Traversable $file    Files to remove. Fileglobs (e.g.  *.c) can be given to remove all matching files
     * @param array                     $options An array of options
     */
    public function rm($file, array $options = [])
    {
        $this->rm->__invoke($file, $options);
    }

    /**
     * Summarize 'git log' output.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $shortlog = $git->shortlog();
     * ```
     *
     * Output Example:
     *
     * ``` php
     * [
     *     'John Doe <john@example.com>' => [
     *         0 => ['commit' => '589de67', 'date' => new \DateTime('2014-02-10 12:56:15 +0300'), 'subject' => 'Update README'],
     *         1 => ['commit' => '589de67', 'date' => new \DateTime('2014-02-15 12:56:15 +0300'), 'subject' => 'Update README'],
     *     ],
     *     //...
     * ]
     * ```
     *
     * @param string|array|\Traversable $commits Defaults to HEAD
     *
     * @return array
     */
    public function shortlog($commits = 'HEAD')
    {
        return $this->shortlog->__invoke($commits);
    }

    /**
     * Shows one or more objects (blobs, trees, tags and commits).
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * echo $git->show('3ddee587e209661c8265d5bfd0df999836f6dfa2');
     * ```
     *
     * Options:
     * - format        (string)  Pretty-print the contents of the commit logs in a given format, where <format> can be one of oneline, short, medium, full, fuller, email, raw and format:<string>
     * - abbrev-commit (boolean) Instead of showing the full 40-byte hexadecimal commit object name, show only a partial prefix
     *
     * @param string $object  The names of objects to show
     * @param array  $options An array of options
     *
     * @return string
     */
    public function show($object, array $options = [])
    {
        return $this->show->__invoke($object, $options);
    }

    /**
     * Save your local modifications to a new stash, and run git reset --hard to revert them.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->stash();
     * ```
     */
    public function stash()
    {
        $this->stash->__invoke();
    }

    /**
     * Returns the working tree status.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $status = $git->status();
     * ```
     *
     * Output Example:
     *
     * ``` php
     * [
     *     'branch' => 'master',
     *     'changes' => [
     *         ['file' => 'item1.txt', 'index' => 'A', 'work_tree' => 'M'],
     *         ['file' => 'item2.txt', 'index' => 'A', 'work_tree' => ' '],
     *         ['file' => 'item3.txt', 'index' => '?', 'work_tree' => '?'],
     *     ]
     * ]
     * ```
     *
     * Options:
     * - ignored (boolean) Show ignored files as well
     *
     * @param string|array|\Traversable $pathSpec Restrict status to these paths
     * @param array                     $options  An array of options
     *
     * @return array
     */
    public function status($pathSpec = null, array $options = [])
    {
        return $this->status->__invoke($pathSpec, $options);
    }

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
     * Output Example:
     *
     * ```
     * ['v1.0.0', 'v1.0.1', 'v1.0.2']
     * ```
     *
     * @return array
     */
    public function tag()
    {
        return $this->tag->__invoke();
    }

    /**
     * Returns the contents of a tree object.
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->clone('https://github.com/kzykhys/PHPGit.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $tree = $git->tree('master');
     * ```
     *
     * Output Example:
     *
     * ``` php
     * [
     *     ['mode' => '100644', 'type' => 'blob', 'hash' => '1f100ce9855b66111d34b9807e47a73a9e7359f3', 'file' => '.gitignore', 'sort' => '2:.gitignore'],
     *     ['mode' => '100644', 'type' => 'blob', 'hash' => 'e0bfe494537037451b09c32636c8c2c9795c05c0', 'file' => '.travis.yml', 'sort' => '2:.travis.yml'],
     *     ['mode' => '040000', 'type' => 'tree', 'hash' => '8d5438e79f77cd72de80c49a413f4edde1f3e291', 'file' => 'bin', 'sort' => '1:.bin'],
     * ]
     * ```
     *
     * @param string $branch The commit
     * @param string $path   The path
     *
     * @return array
     */
    public function tree($branch = 'master', $path = '')
    {
        return $this->tree->__invoke($branch, $path);
    }
}

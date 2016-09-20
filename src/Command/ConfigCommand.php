<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Get and set repository or global options - `git config`.
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class ConfigCommand extends Command
{
    /**
     * @see \PHPGit\Git::config()
     *
     * @param array $options An array of options
     *
     * @return array
     */
    public function __invoke(array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('config')
            ->add('--list')
            ->add('--null');

        $this->addFlags($builder, $options, ['global', 'system']);

        $config = [];
        $output = $this->git->run($builder->getProcess());
        $lines  = $this->split($output, true);

        foreach ($lines as $line) {
            list($name, $value) = explode("\n", $line, 2);

            if (isset($config[$name])) {
                $config[$name] .= "\n".$value;
            } else {
                $config[$name] = $value;
            }
        }

        return $config;
    }

    /**
     * Set an option.
     *
     * Options:
     * - global (boolean) Read or write configuration options for the current user
     * - system (boolean) Read or write configuration options for all users on the current machine
     *
     * @param string $name    The name of the option
     * @param string $value   The value to set
     * @param array  $options An array of options
     */
    public function set($name, $value, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('config');

        $this->addFlags($builder, $options, ['global', 'system']);

        $builder->add($name)->add($value);
        $process = $builder->getProcess();
        $this->git->run($process);
    }

    /**
     * Adds a new line to the option without altering any existing values.
     *
     * Options:
     * - global (boolean) Read or write configuration options for the current user
     * - system (boolean) Read or write configuration options for all users on the current machine
     *
     * @param string $name    The name of the option
     * @param string $value   The value to add
     * @param array  $options An array of options
     */
    public function add($name, $value, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('config');

        $this->addFlags($builder, $options, ['global', 'system']);

        $builder->add('--add')->add($name)->add($value);
        $process = $builder->getProcess();
        $this->git->run($process);
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'global' => false,
            'system' => false,
        ]);
    }
}

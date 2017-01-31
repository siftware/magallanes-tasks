<?php
namespace Siftware\MagallanesTasks;

use Symfony\Component\Process\Process;
use Mage\Task\Exception\ErrorException;
use Mage\Task\AbstractTask;

class RunScripts extends AbstractTask
{
    public function getName()
    {
        return 'siftware/run-script';
    }

    public function getDescription()
    {
        return '[Siftware] Run script on remote';
    }

    public function execute()
    {
        $globalOptions = $this->runtime->getConfigOption('runscripts', []);
        $envOptions = $this->runtime->getEnvOption('runscripts', []);
        $options = array_merge(
            (is_array($globalOptions) ? $globalOptions : []),
            (is_array($envOptions) ? $envOptions : []),
            $this->options
        );

        foreach ($options as $script) {
            $name = isset($script['name']) ? $script['name'] : false;
            if (!$name) {
                continue;
            }
            $command = "chmod +x {$name} && ./{$name}";
            if (isset($script['vars'])) {
                foreach ($script['vars'] as $var => $value) {
                    if (!is_numeric($var)) {
                        $command .= " -{$var} {$value}";
                    } else {
                        $command .= " {$value}";
                    }
                }
            }

            $process = $this->runtime->runCommand($command);
            if (!$process->isSuccessful()) {
                return false;
            }
        }
        return true;
    }
}

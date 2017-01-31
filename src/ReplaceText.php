<?php
namespace Siftware\MagallanesTasks;

use Symfony\Component\Process\Process;
use Mage\Task\Exception\ErrorException;
use Mage\Task\AbstractTask;

class ReplaceText extends AbstractTask
{
    public function getName()
    {
        return 'siftware/replace-text';
    }

    public function getDescription()
    {
        return '[Siftware] Replace placeholder text in files';
    }

    public function execute()
    {
        $globalOptions = $this->runtime->getConfigOption('replacetext', []);
        $envOptions = $this->runtime->getEnvOption('replacetext', []);
        $options = array_merge(
            (is_array($globalOptions) ? $globalOptions : []),
            (is_array($envOptions) ? $envOptions : []),
            $this->options
        );

        foreach ($options['files'] as $file) {
            $command = "sed -iE 's/{$options['from']}/{$options['to']}/' '{$file}'";

            $process = $this->runtime->runCommand($command);
            if (!$process->isSuccessful()) {
                return false;
            }
        }

        return true;
    }
}

<?php

declare(strict_types=1);

namespace Snippet\Command\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SnippetCommand extends Command
{
    /**
     * @var State
     */
    private $appState;

    /**
     * @param State $appState
     * @param string|null $name
     */
    public function __construct(State $appState, string $name = null)
    {
        parent::__construct($name);
        $this->appState = $appState;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('The command for demonstration.');
        $this->setDefinition([
            new InputOption('option-flag', 'f', InputOption::VALUE_NONE, 'Option flag'),
            new InputOption('option-value', 'o', InputOption::VALUE_REQUIRED, 'Option value'),
            new InputArgument('arg', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'tokens to search for'),
        ]);
        $this->setHelp(<<<EOT
Demonstrates usage of custom commands
<info>Contains options and attributes, works in different arias</info>
EOT
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->argumentAndOptionExample($input, $output);
            $this->emulateDifferentAreasExample($output);
            $this->emulateEnvironmentExample();
        } catch (LocalizedException $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return Cli::RETURN_FAILURE;
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function argumentAndOptionExample(InputInterface $input, OutputInterface $output)
    {
        $argument = implode(' ', $input->getArgument('arg'));
        $output->writeln("<comment>$argument</comment>");

        $optionFlag = $input->getOption('option-flag');
        if ($optionFlag) {
            $output->writeln("<info>option-flag is set</info>");
        } else {
            $output->writeln("<error>option-flag is not set</error>");
        }

        $optionValue = $input->getOption('option-value');
        if ($optionValue) {
            $output->writeln("<info>option-value is set with value: $optionValue</info>");
        } else {
            $output->writeln("<error>option-value is not set</error>");
        }
    }

    /**
     * Example of emulating different areas
     *
     * @param OutputInterface $output
     * @throws \Exception
     */
    private function emulateDifferentAreasExample(OutputInterface $output)
    {
        $this->appState->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this, 'adminhtmlSpecificCode'],
            [$output, 'adminhtmlSpecificCode']
        );

        $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$this, 'frontendSpecificCode'],
            [$output, 'frontendSpecificCode']
        );
    }

    /**
     * Example of emulating of different environments
     *
     * if you need to emulate a store scope:
     * \Magento\Store\Model\App\Emulation:startEnvironmentEmulation
     * \Magento\Store\Model\App\Emulation:stopEnvironmentEmulation
     * @return void
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
     */
    private function emulateEnvironmentExample()
    {
        // @todo: implement
    }

    /**
     * Contains adminhtml specific code
     *
     * @param OutputInterface $output
     * @param string $message
     * @throws LocalizedException
     */
    public function adminhtmlSpecificCode(OutputInterface $output, string $message): void
    {
        $output->writeln("<info>$message</info>");
        $output->writeln("<comment>{$this->appState->getAreaCode()}</comment>");
    }

    /**
     * Contains frontend specific code
     *
     * @param OutputInterface $output
     * @param string $message
     * @throws LocalizedException
     */
    public function frontendSpecificCode(OutputInterface $output, string $message): void
    {
        $output->writeln("<info>$message</info>");
        $output->writeln("<comment>{$this->appState->getAreaCode()}</comment>");
    }
}

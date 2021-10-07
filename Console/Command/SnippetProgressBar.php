<?php

declare(strict_types=1);

namespace Snippet\Command\Console\Command;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;

class SnippetProgressBar extends Command
{
    /**
     * @var ProgressBarFactory
     */
    private $progressBarFactory;

    /**
     * @var CollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @param ProgressBarFactory $progressBarFactory
     * @param CollectionFactory $countryCollectionFactory
     * @param string|null $name
     */
    public function __construct(
        ProgressBarFactory $progressBarFactory,
        CollectionFactory $countryCollectionFactory,
        string $name = null
    ) {
        parent::__construct($name);
        $this->progressBarFactory = $progressBarFactory;
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('The command progress bar for demonstration.');
        $this->setHelp(<<<EOT
Demonstrates usage of custom commands
<info>Contains progress bar</info>
EOT
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Start</info>');

        $countries = $this->countryCollectionFactory->create()->getItems();
        $progressBar = $this->getStartedProgressBar($output, count($countries));

        foreach ($countries as $country) {
            $country->getRegions();
            $progressBar->advance();
        }

        $output->write(PHP_EOL);
        $output->writeln('<info>Finish</info>');

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @param int $steps
     * @return ProgressBar
     */
    public function getStartedProgressBar(OutputInterface $output, int $steps): ProgressBar
    {
        /** @var ProgressBar $progress */
        $progressBar = $this->progressBarFactory->create(
            [
                'output' => $output,
                'max' => $steps,
            ]
        );

        $progressBar->setFormat(
            '%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%'
        );

        $progressBar->start();

        return $progressBar;
    }
}

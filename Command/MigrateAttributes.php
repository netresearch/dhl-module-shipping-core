<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Command;

use Dhl\ShippingCore\Model\Attribute\Migrate;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateAttributes
 * @package Dhl\ShippingCore\Command
 */
class MigrateAttributes extends Command
{
    /**
     * @var Migrate
     */
    private $migrate;

    /**
     * @var State
     */
    private $appState;

    /**
     * MigrateAttributes constructor.
     *
     * @param Migrate $migrate
     * @param State $appState
     * @param string|null $name
     */
    public function __construct(
        Migrate $migrate,
        State $appState,
        string $name = null
    ) {
        $this->migrate = $migrate;
        $this->appState = $appState;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('dhlgw:migrate-attributes');
        $this->setDescription('Migrate Eav Attributes from DHL Shipping to DHL Global Webservices');

        parent::configure();
    }

    /**
     * Execute Attribute migration.
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->getAreaCode();
        } catch (LocalizedException $exception) {
            $this->appState->setAreaCode(Area::AREA_GLOBAL);
        }
        $output->writeln('Start DHL attribute migration...');

        $result = $this->migrate->runAttributeMigration();

        if (!empty($result)) {
            foreach ($result as $error) {
                $output->writeln($error);
            }
            $output->writeln('Could not perform DHL attribute migration completely.');
        } else {
            $output->writeln('Finished DHL attribute migration.');
        }
    }
}

<?php declare(strict_types=1);

namespace OndraM\CiDetector\Command;

use Laminas\Filter\FilterInterface;
use Laminas\Filter\Word\DashToCamelCase;
use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\CiMeta;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    /** @var FilterInterface */
    private $propertyNameFilter;
    /** @var CiMeta */
    private $ciMeta;
    /** @var CiDetector */
    private $ciDetector;

    public function __construct(CiDetector $ciDetector, string $name = null)
    {
        $this->propertyNameFilter = new DashToCamelCase();
        $this->ciMeta = new CiMeta();
        $this->ciDetector = $ciDetector;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('dump')
            ->setDescription('Dump CI values from current environment');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->ciDetector->isCiDetected()) {
            throw new RuntimeException('No CI environment detected');
        }

        $ci = $this->ciDetector->detect();

        $table = new Table($output);
        $table->setHeaders(['Property name', 'Current value']);

        foreach ($ci->describe() as $property => $value) {
            $table->addRow([$property, $value]);
        }

        $table->render();

        return 0;
    }
}

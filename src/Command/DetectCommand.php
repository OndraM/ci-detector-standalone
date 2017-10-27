<?php declare(strict_types=1);

namespace OndraM\CiDetector\Command;

use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\CiMeta;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetectCommand extends Command
{
    const ARGUMENT_PROPERTY = 'property';
    /** @var CiMeta */
    private $ciMeta;
    /** @var CiDetector */
    private $ciDetector;

    public function __construct(CiDetector $ciDetector, string $name = null)
    {
        $this->ciMeta = new CiMeta();
        $this->ciDetector = $ciDetector;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $availableProperties = $this->ciMeta->getAvailableProperties();

        $this->setName('detect')
            ->setDescription('Detect properties of CI run environment')
            ->addArgument(
                self::ARGUMENT_PROPERTY,
                InputArgument::OPTIONAL,
                'Name of the property to detect. '
                    . '(<comment>' . implode('</comment>, <comment>', $availableProperties) . '</comment>)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->ciDetector->isCiDetected()) {
            return 1;
        }

        $propertyToGet = $input->getArgument(self::ARGUMENT_PROPERTY);

        if (empty($propertyToGet)) {
            return 0;
        }

        $output->writeln($this->detectProperty($propertyToGet));

        return 0;
    }

    private function detectProperty(string $propertyName): string
    {
        $getterMethod = $this->ciMeta->assembleMethodNameFromProperty($propertyName);
        $ci = $this->ciDetector->detect();
        $callable = [$ci, $getterMethod];

        if (!is_callable($callable)) {
            throw new InvalidArgumentException(
                sprintf('Unknown property "%s".', $propertyName)
            );
        }

        return call_user_func($callable);
    }
}

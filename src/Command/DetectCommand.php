<?php

namespace OndraM\CiDetector\Command;

use OndraM\CiDetector\CiDetector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Filter\FilterInterface;
use Zend\Filter\Word\DashToCamelCase;

class DetectCommand extends Command
{
    const ARGUMENT_PROPERTY = 'property';
    /** @var FilterInterface */
    private $propertyNameFilter;
    /** @var CiDetector */
    private $ciDetector;

    /**
     * @param CiDetector $ciDetector
     * @param string $name
     */
    public function __construct(CiDetector $ciDetector, $name = null)
    {
        $this->propertyNameFilter = new DashToCamelCase();
        $this->ciDetector = $ciDetector;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('detect')
            ->setDescription('Detect properties of CI run environment')
            ->addArgument(
                self::ARGUMENT_PROPERTY,
                InputArgument::OPTIONAL,
                'Name of the property to detect.'
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

    /**
     * @param string $propertyName
     * @return string
     */
    private function detectProperty($propertyName)
    {
        $getterMethod = $this->assembleMethodName($propertyName);
        $ci = $this->ciDetector->detect();
        $callable = [$ci, $getterMethod];

        if (!is_callable($callable)) {
            throw new InvalidArgumentException(
                sprintf('Unknown property "%s".', $propertyName)
            );
        }

        return call_user_func($callable);
    }

    /**
     * @param string $propertyName
     * @return string
     */
    private function assembleMethodName($propertyName)
    {
        $methodName = $this->propertyNameFilter->filter($propertyName);

        return 'get' . $methodName;
    }
}

<?php declare(strict_types=1);

namespace OndraM\CiDetector;

use Laminas\Filter\FilterInterface;
use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Filter\Word\DashToCamelCase;

/**
 * Provide metadata of CiInterface
 */
class CiMeta
{
    /** @var FilterInterface */
    private $propertyNameFilter;
    /** @var FilterInterface */
    private $methodNameFilter;

    public function __construct()
    {
        $this->propertyNameFilter = new DashToCamelCase();
        $this->methodNameFilter = new CamelCaseToDash();
    }

    public function assembleMethodNameFromProperty(string $propertyName): string
    {
        $methodName = $this->propertyNameFilter->filter($propertyName);

        if (mb_strpos($propertyName, 'is') === 0) {
            return lcfirst($methodName);
        }

        return 'get' . $methodName;
    }
}

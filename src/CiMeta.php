<?php declare(strict_types=1);

namespace OndraM\CiDetector;

use Laminas\Filter\FilterInterface;
use Laminas\Filter\Word\CamelCaseToDash;
use Laminas\Filter\Word\DashToCamelCase;
use OndraM\CiDetector\Ci\CiInterface;

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

        return 'get' . $methodName;
    }

    /**
     * @return string[]
     */
    public function getAvailableProperties(): array
    {
        $properties = [];

        foreach (get_class_methods(CiInterface::class) as $methodName) {
            if (mb_substr($methodName, 0, 3) !== 'get') {
                continue;
            }

            $properties[] = $this->derivePropertyNameFromMethod($methodName);
        }

        return $properties;
    }

    private function derivePropertyNameFromMethod(string $methodName): string
    {
        $methodName = mb_substr($methodName, 3);

        return mb_strtolower($this->methodNameFilter->filter($methodName));
    }
}

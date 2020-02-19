<?php declare(strict_types=1);

namespace OndraM\CiDetector;

use OndraM\CiDetector\Ci\CiInterface;
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

        if (method_exists(CiInterface::class, 'get' . $methodName)) {
            return 'get' . $methodName;
        }

        return 'is' . $methodName;
    }

    /**
     * @return string[]
     */
    public function getAvailableProperties(): array
    {
        $properties = [];

        foreach (get_class_methods(CiInterface::class) as $methodName) {
            if (mb_substr($methodName, 0, 3) !== 'get'
                && (mb_substr($methodName, 0, 2) !== 'is'
                    || $methodName === 'isDetected')) {
                continue;
            }

            $properties[] = $this->derivePropertyNameFromMethod($methodName);
        }

        return $properties;
    }

    private function derivePropertyNameFromMethod(string $methodName): string
    {
        $methodName = (mb_substr($methodName, 0, 3) === 'get') ? mb_substr($methodName, 3) : mb_substr($methodName, 2);

        return mb_strtolower($this->methodNameFilter->filter($methodName));
    }
}

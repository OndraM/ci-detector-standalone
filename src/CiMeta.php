<?php

namespace OndraM\CiDetector;

use OndraM\CiDetector\Ci\CiInterface;
use Zend\Filter\FilterInterface;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\Filter\Word\DashToCamelCase;

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

    /**
     * @param string $propertyName
     * @return string
     */
    public function assembleMethodNameFromProperty($propertyName)
    {
        $methodName = $this->propertyNameFilter->filter($propertyName);

        return 'get' . $methodName;
    }

    public function getAvailableProperties()
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

    /**
     * @param string $methodName
     * @return string
     */
    private function derivePropertyNameFromMethod($methodName)
    {
        $methodName = mb_substr($methodName, 3);

        return mb_strtolower($this->methodNameFilter->filter($methodName));
    }
}

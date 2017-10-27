<?php

namespace OndraM\CiDetector;

use PHPUnit\Framework\TestCase;

/**
 * @covers \OndraM\CiDetector\CiMeta
 */
class CiMetaTest extends TestCase
{
    /**
     * @dataProvider providePropertyNames
     * @param string $property
     * @param string $expectedMethodName
     */
    public function testShouldAssembleMethodNameFromProperty($property, $expectedMethodName)
    {
        $meta = new CiMeta();

        $this->assertSame($expectedMethodName, $meta->assembleMethodNameFromProperty($property));
    }

    /**
     * @return array[]
     */
    public function providePropertyNames()
    {
        return [
            ['foo', 'getFoo'],
            ['ci-name', 'getCiName'],
        ];
    }

    public function testShouldGetPropertyNamesFromTheCiInterface()
    {
        $meta = new CiMeta();

        $this->assertEquals(
            [
                'ci-name',
                'build-number',
                'build-url',
                'git-commit',
                'git-branch',
                'repository-url',
            ],
            $meta->getAvailableProperties()
        );
    }
}

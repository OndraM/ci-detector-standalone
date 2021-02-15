<?php declare(strict_types=1);

namespace OndraM\CiDetector\Tests;

use OndraM\CiDetector\CiMeta;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OndraM\CiDetector\CiMeta
 */
class CiMetaTest extends TestCase
{
    /**
     * @dataProvider providePropertyNames
     *
     * @test
     */
    public function shouldAssembleMethodNameFromProperty(string $property, string $expectedMethodName): void
    {
        $meta = new CiMeta();

        $this->assertSame($expectedMethodName, $meta->assembleMethodNameFromProperty($property));
    }

    /**
     * @return array[]
     */
    public function providePropertyNames(): array
    {
        return [
            ['is-pull-request', 'isPullRequest'],
            ['ci-name', 'getCiName'],
            ['branch', 'getBranch'],
        ];
    }
}

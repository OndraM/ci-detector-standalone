<?php

namespace OndraM\CiDetector\Tests\Command;

use OndraM\CiDetector\Ci\CiInterface;
use OndraM\CiDetector\Ci\Travis;
use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\Command\DumpCommand;
use OndraM\CiDetector\Exception\CiNotDetectedException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \OndraM\CiDetector\Command\DumpCommand
 */
class DumpCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldThrowExceptionIfCiNotDetected()
    {
        $ciDetectorMock = $this->createCiDetectorForNonCiEnvironment();
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No CI environment detected');
        $tester->execute(['command' => $command->getName()]);
    }

    public function testShouldDumpDetectedCiValue()
    {
        $ciMock = $this->createConfiguredMock(
            Travis::class,
            [
                'getCiName' => 'Jenkins',
                'getBuildNumber' => '1337',
                'getBuildUrl' => '',
                'getGitCommit' => '0f00c556508e02b9376a39ce21f25cd79e9183f4',
                'getGitBranch' => 'origin/feature/foo',
                'getRepositoryUrl' => 'ssh://git@gitserver:7999/project/repo.git',
            ]
        );

        $ciDetectorMock = $this->createCiDetectorForCiEnvironment($ciMock);
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $tester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $expectedOutput = <<<HTXT
+----------------+-------------------------------------------+
| Property name  | Current value                             |
+----------------+-------------------------------------------+
| ci-name        | Jenkins                                   |
| build-number   | 1337                                      |
| build-url      |                                           |
| git-commit     | 0f00c556508e02b9376a39ce21f25cd79e9183f4  |
| git-branch     | origin/feature/foo                        |
| repository-url | ssh://git@gitserver:7999/project/repo.git |
+----------------+-------------------------------------------+

HTXT;

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame($expectedOutput, $tester->getDisplay(true));
    }

    /**
     * @param CiDetector $ciDetector
     * @return Command
     */
    private function createCommandWithCiDetectorMock(CiDetector $ciDetector)
    {
        $application = new Application();
        $application->add(new DumpCommand($ciDetector));

        return $application->find('dump');
    }

    /**
     * @param CiInterface $ci
     * @return CiDetector::class|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createCiDetectorForCiEnvironment(CiInterface $ci)
    {
        return $this->createConfiguredMock(
            CiDetector::class,
            [
                'isCiDetected' => true,
                'detect' => $ci,
            ]
        );
    }

    /**
     * @return CiDetector::class|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createCiDetectorForNonCiEnvironment()
    {
        $ciDetectorMock = $this->createMock(CiDetector::class);
        $ciDetectorMock->expects($this->any())
            ->method('isCiDetected')
            ->willReturn(false);
        $ciDetectorMock->expects($this->any())
            ->method('detect')
            ->willThrowException(new CiNotDetectedException());

        return $ciDetectorMock;
    }
}

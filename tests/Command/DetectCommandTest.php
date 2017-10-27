<?php

namespace OndraM\CiDetector\Tests\Command;

use OndraM\CiDetector\Ci\CiInterface;
use OndraM\CiDetector\Ci\Travis;
use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\Command\DetectCommand;
use OndraM\CiDetector\Exception\CiNotDetectedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \OndraM\CiDetector\Command\DetectCommand
 */
class DetectCommandTest extends TestCase
{
    public function testShouldReturnNonZeroStatusCodeIfCiNotDetected()
    {
        $ciDetectorMock = $this->createCiDetectorForNonCiEnvironment();
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $tester->execute(['command' => $command->getName()]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertSame('', $tester->getDisplay());
    }

    public function testShouldReturnZeroStatusCodeIfCiIsDetected()
    {
        $ciDetectorMock = $this->createCiDetectorForCiEnvironment($this->createMock(CiInterface::class));
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $tester->execute(['command' => $command->getName()]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('', $tester->getDisplay());
    }

    public function testShouldOutputPropertyValue()
    {
        $ciMock = $this->createConfiguredMock(
            Travis::class,
            [
                'getGitBranch' => 'origin/feature/foo',
            ]
        );

        $ciDetectorMock = $this->createCiDetectorForCiEnvironment($ciMock);
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $tester->execute(
            [
                'command' => $command->getName(),
                DetectCommand::ARGUMENT_PROPERTY => 'git-branch',
            ]
        );

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame("origin/feature/foo\n", $tester->getDisplay(true));
    }

    public function testShouldThrowExceptionIfCiPropertyIsNotSupported()
    {
        $ciDetectorMock = $this->createCiDetectorForCiEnvironment($this->createMock(CiInterface::class));
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown property "some-not-supported-property"');

        $tester->execute(
            [
                'command' => $command->getName(),
                DetectCommand::ARGUMENT_PROPERTY => 'some-not-supported-property',
            ]
        );
    }

    /**
     * @param CiDetector $ciDetector
     * @return Command
     */
    private function createCommandWithCiDetectorMock(CiDetector $ciDetector)
    {
        $application = new Application();
        $application->add(new DetectCommand($ciDetector));

        return $application->find('detect');
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

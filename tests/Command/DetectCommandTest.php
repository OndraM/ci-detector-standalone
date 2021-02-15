<?php declare(strict_types=1);

namespace OndraM\CiDetector\Tests\Command;

use OndraM\CiDetector\Ci\CiInterface;
use OndraM\CiDetector\Ci\Travis;
use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\Command\DetectCommand;
use OndraM\CiDetector\Exception\CiNotDetectedException;
use PHPUnit\Framework\MockObject\MockObject;
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
    /**
     * @test
     */
    public function shouldReturnNonZeroStatusCodeIfCiNotDetected(): void
    {
        $ciDetectorMock = $this->createCiDetectorForNonCiEnvironment();
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $tester->execute(['command' => $command->getName()]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertSame('', $tester->getDisplay());
    }

    /**
     * @test
     */
    public function shouldReturnZeroStatusCodeIfCiIsDetected(): void
    {
        $ciDetectorMock = $this->createCiDetectorForCiEnvironment($this->createMock(CiInterface::class));
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $tester->execute(['command' => $command->getName()]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('', $tester->getDisplay());
    }

    /**
     * @test
     */
    public function shouldOutputPropertyValue(): void
    {
        $ciMock = $this->createConfiguredMock(
            Travis::class,
            [
                'describe' => ['branch' => 'origin/feature/foo'],
            ]
        );

        $ciDetectorMock = $this->createCiDetectorForCiEnvironment($ciMock);
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $tester->execute(
            [
                'command' => $command->getName(),
                DetectCommand::ARGUMENT_PROPERTY => 'branch',
            ]
        );

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame("origin/feature/foo\n", $tester->getDisplay(true));
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfCiPropertyIsNotSupported(): void
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

    private function createCommandWithCiDetectorMock(CiDetector $ciDetector): Command
    {
        $application = new Application();
        $application->add(new DetectCommand($ciDetector));

        return $application->find('detect');
    }

    /**
     * @return CiDetector|MockObject
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
     * @return CiDetector|MockObject
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

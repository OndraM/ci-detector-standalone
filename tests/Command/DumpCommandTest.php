<?php declare(strict_types=1);

namespace OndraM\CiDetector\Tests\Command;

use OndraM\CiDetector\Ci\CiInterface;
use OndraM\CiDetector\Ci\Travis;
use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\Command\DumpCommand;
use OndraM\CiDetector\Exception\CiNotDetectedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \OndraM\CiDetector\Command\DumpCommand
 */
class DumpCommandTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionIfCiNotDetected(): void
    {
        $ciDetectorMock = $this->createCiDetectorForNonCiEnvironment();
        $command = $this->createCommandWithCiDetectorMock($ciDetectorMock);
        $tester = new CommandTester($command);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No CI environment detected');
        $tester->execute(['command' => $command->getName()]);
    }

    /**
     * @test
     */
    public function shouldDumpDetectedCiValue(): void
    {
        $ciMock = $this->createConfiguredMock(
            Travis::class,
            [
                'describe' => [
                    'ci-name' => 'Jenkins',
                    'build-number' => '1337',
                    'build-url' => 'http://jenkins.foo/job/foo_job_name_1337/',
                    'commit' => '0f00c556508e02b9376a39ce21f25cd79e9183f4',
                    'branch' => 'origin/feature/foo',
                    'target-branch' => 'origin/main',
                    'repository-name' => '',
                    'repository-url' => 'ssh://git@gitserver:7999/project/repo.git',
                    'is-pull-request' => 'Yes',
                ],
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
+-----------------+-------------------------------------------+
| Property name   | Current value                             |
+-----------------+-------------------------------------------+
| ci-name         | Jenkins                                   |
| build-number    | 1337                                      |
| build-url       | http://jenkins.foo/job/foo_job_name_1337/ |
| commit          | 0f00c556508e02b9376a39ce21f25cd79e9183f4  |
| branch          | origin/feature/foo                        |
| target-branch   | origin/main                               |
| repository-name |                                           |
| repository-url  | ssh://git@gitserver:7999/project/repo.git |
| is-pull-request | Yes                                       |
+-----------------+-------------------------------------------+

HTXT;

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame($expectedOutput, $tester->getDisplay(true));
    }

    private function createCommandWithCiDetectorMock(CiDetector $ciDetector): Command
    {
        $application = new Application();
        $application->add(new DumpCommand($ciDetector));

        return $application->find('dump');
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

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Integration\Console;

use Isfett\PhpAnalyzer\Console\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ApplicationTest
 */
class ApplicationTest extends TestCase
{
    const APPLICATION_INFO = 'php-analyzer 1.0.0 by Christopher Stenke <chris@isfett.com>';
    /** @var Application */
    private $application;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->application = new Application();
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testApplicationAndAuthorInfo(): void
    {
        $input = new ArrayInput([]);

        $output = new BufferedOutput();
        $this->application->doRun($input, $output);

        $this->assertStringStartsWith(
            self::APPLICATION_INFO,
            $output->fetch()
        );
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testApplicationAndAuthorInfoIsHiddenWhenInQuietMode(): void
    {
        $input = new ArrayInput([
            '--quiet' => true,
        ]);

        $output = new BufferedOutput();
        $exitCode = $this->application->doRun($input, $output);

        $this->assertStringStartsWith(
            'Description',
            $output->fetch()
        );
        $this->assertEquals(Application::EXIT_CODE_SUCCESS, $exitCode);

        $input = new ArrayInput([
            '-q' => true,
        ]);

        $output = new BufferedOutput();
        $exitCode = $this->application->doRun($input, $output);

        $this->assertStringStartsWith(
            'Description',
            $output->fetch()
        );
        $this->assertEquals(Application::EXIT_CODE_SUCCESS, $exitCode);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testApplicationVerswion(): void
    {
        $input = new ArrayInput([
            '--version' => true,
        ]);

        $output = new BufferedOutput();
        $exitCode = $this->application->doRun($input, $output);

        $this->assertStringStartsWith(
            self::APPLICATION_INFO,
            $output->fetch()
        );
        $this->assertEquals(Application::EXIT_CODE_SUCCESS, $exitCode);

        $input = new ArrayInput([
            '-v' => true,
        ]);

        $output = new BufferedOutput();
        $this->application->doRun($input, $output);

        $this->assertStringStartsWith(
            self::APPLICATION_INFO,
            $output->fetch()
        );
        $this->assertEquals(Application::EXIT_CODE_SUCCESS, $exitCode);
    }
}

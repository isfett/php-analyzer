<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Integration\Console;

use Isfett\PhpAnalyzer\Builder\FinderBuilder;
use Isfett\PhpAnalyzer\Builder\ProcessorBuilder;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilder;
use Isfett\PhpAnalyzer\Builder\VisitorBuilder;
use Isfett\PhpAnalyzer\Console\Application;
use Isfett\PhpAnalyzer\Console\Command\MagicNumberDetectorCommand;
use Isfett\PhpAnalyzer\Node\ProcessorRunner;
use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Service\SortService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class MagicNumberDetectorCommandTest
 */
class MagicNumberDetectorCommandTest extends TestCase
{
    /** @var Application */
    private $application;

    /** @var MagicNumberDetectorCommand */
    private $magicNumberDetectorCommand;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->magicNumberDetectorCommand = new MagicNumberDetectorCommand(
            new FinderBuilder(),
            new NodeRepresentationService(),
            new ProcessorBuilder(),
            new ProcessorRunner(),
            new SortConfigurationBuilder(),
            new SortService(),
            new VisitorBuilder()
        );

        $this->application = new Application();
        $this->application->addCommands([$this->magicNumberDetectorCommand]);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testRun(): void
    {
        $input = new ArrayInput([
            'magic-number-detector',
            'directory' => __DIR__ . '/../../../../tests/data',
            '--include-files' => 'magic_number_detector_integrationtest.php',
            '--visitors' => 'Argument,Array,Assign,Condition,DefaultParameter,Operation,Property,Return,SwitchCase,Ternary',
            '--processors' => 'IgnoreArrayKey,IgnoreOne,IgnoreZero,IgnoreDefineFunction,IgnoreForLoop',

        ], $this->magicNumberDetectorCommand->getDefinition());

        $output = new BufferedOutput();
        $exitCode = $this->magicNumberDetectorCommand->run($input, $output);
        $outputText = $output->fetch();

        $this->assertSame(Application::EXIT_CODE_FAILURE, $exitCode);
        $this->assertStringStartsWith(
            '<command-start>Starting magic-number-detector command</command-start>',
            $outputText
        );
        $this->assertStringContainsString('Processors processed magic numbers. Magic numbers found:', $outputText);

        $expectedOutput = <<<EOT
+--------------------------------------------------------------+----------------------------------------------+
| Number                                                       | Occurrence                                   |
+--------------------------------------------------------------+----------------------------------------------+
| \$input === <focus>-5</focus>                                 | magic_number_detector_integrationtest.php:38 |
|--------------------------------------------------------------|----------------------------------------------|
| return <focus>-1</focus>                                     | magic_number_detector_integrationtest.php:51 |
|--------------------------------------------------------------|----------------------------------------------|
| \$input > <focus>2</focus>                                    | magic_number_detector_integrationtest.php:14 |
|--------------------------------------------------------------|----------------------------------------------|
| 1 === 0 ? <focus>2</focus> : 3                               | magic_number_detector_integrationtest.php:70 |
|--------------------------------------------------------------|----------------------------------------------|
| 1 === 0 ? 2 : <focus>3</focus>                               | magic_number_detector_integrationtest.php:70 |
|--------------------------------------------------------------|----------------------------------------------|
| floatval(<focus>3.14</focus>)                                | magic_number_detector_integrationtest.php:58 |
|--------------------------------------------------------------|----------------------------------------------|
| round(\$input, <focus>4</focus>)                              | magic_number_detector_integrationtest.php:25 |
|--------------------------------------------------------------|----------------------------------------------|
| ?int \$input = <focus>4</focus>                               | magic_number_detector_integrationtest.php:13 |
|--------------------------------------------------------------|----------------------------------------------|
| case <focus>5</focus>:                                       | magic_number_detector_integrationtest.php:20 |
|--------------------------------------------------------------|----------------------------------------------|
| private \$variable = <focus>6</focus>                         | magic_number_detector_integrationtest.php:11 |
|--------------------------------------------------------------|----------------------------------------------|
| \$input > <focus>7</focus>                                    | magic_number_detector_integrationtest.php:26 |
|--------------------------------------------------------------|----------------------------------------------|
| 'age' => <focus>13</focus>                                   | magic_number_detector_integrationtest.php:30 |
|--------------------------------------------------------------|----------------------------------------------|
| return <focus>15</focus>                                     | magic_number_detector_integrationtest.php:15 |
|--------------------------------------------------------------|----------------------------------------------|
| \$input * <focus>15</focus>                                   | magic_number_detector_integrationtest.php:41 |
|--------------------------------------------------------------|----------------------------------------------|
| \$input > <focus>18</focus>                                   | magic_number_detector_integrationtest.php:31 |
|--------------------------------------------------------------|----------------------------------------------|
| <focus>18</focus>                                            | magic_number_detector_integrationtest.php:32 |
|--------------------------------------------------------------|----------------------------------------------|
| <focus>20</focus> * 21                                       | magic_number_detector_integrationtest.php:44 |
|--------------------------------------------------------------|----------------------------------------------|
| 20 * <focus>21</focus>                                       | magic_number_detector_integrationtest.php:44 |
|--------------------------------------------------------------|----------------------------------------------|
| intval(<focus>100</focus>)                                   | magic_number_detector_integrationtest.php:57 |
|--------------------------------------------------------------|----------------------------------------------|
| '1234' => <focus>1234</focus>                                | magic_number_detector_integrationtest.php:34 |
|--------------------------------------------------------------|----------------------------------------------|
| \$a[<focus>1234</focus>]                                      | magic_number_detector_integrationtest.php:65 |
|--------------------------------------------------------------|----------------------------------------------|
| 123 => <focus>1234</focus>                                   | magic_number_detector_integrationtest.php:33 |
+--------------------------------------------------------------+----------------------------------------------+
EOT;
        $this->assertStringContainsString($expectedOutput, $outputText);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testRunWillGiveExitCode0WhenNoMagicNumbersWereFound(): void
    {
        $input = new ArrayInput([
            'magic-number-detector',
            'directory' => __DIR__ . '/../../../../tests/data',
            '--include-files' => 'empty.php',
            '--visitors' => 'Argument,Array,Assign,Condition,DefaultParameter,Operation,Property,Return,SwitchCase',
            '--processors' => 'IgnoreArrayKey,IgnoreOne,IgnoreZero,IgnoreDefineFunction,IgnoreForLoop',

        ], $this->magicNumberDetectorCommand->getDefinition());

        $output = new BufferedOutput();
        $exitCode = $this->magicNumberDetectorCommand->run($input, $output);
        $outputText = $output->fetch();

        $this->assertSame(Application::EXIT_CODE_SUCCESS, $exitCode);
        $this->assertStringStartsWith(
            '<command-start>Starting magic-number-detector command</command-start>',
            $outputText
        );
    }
}

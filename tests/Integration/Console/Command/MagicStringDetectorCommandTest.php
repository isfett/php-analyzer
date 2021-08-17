<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Integration\Console;

use Isfett\PhpAnalyzer\Builder\FinderBuilder;
use Isfett\PhpAnalyzer\Builder\ProcessorBuilder;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilder;
use Isfett\PhpAnalyzer\Builder\VisitorBuilder;
use Isfett\PhpAnalyzer\Console\Application;
use Isfett\PhpAnalyzer\Console\Command\MagicNumberDetectorCommand;
use Isfett\PhpAnalyzer\Console\Command\MagicStringDetectorCommand;
use Isfett\PhpAnalyzer\Node\ProcessorRunner;
use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Service\SortService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class MagicStringDetectorCommandTest
 */
class MagicStringDetectorCommandTest extends TestCase
{
    /** @var Application */
    private $application;

    /** @var MagicStringDetectorCommand */
    private $magicStringDetectorCommand;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->magicStringDetectorCommand = new MagicStringDetectorCommand(
            new FinderBuilder(),
            new NodeRepresentationService(),
            new ProcessorBuilder(),
            new ProcessorRunner(),
            new SortConfigurationBuilder(),
            new SortService(),
            new VisitorBuilder()
        );

        $this->application = new Application();
        $this->application->addCommands([$this->magicStringDetectorCommand]);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testRun(): void
    {
        $input = new ArrayInput([
            'magic-string-detector',
            'directory' => __DIR__ . '/../../../../tests/data',
            '--include-files' => 'magic_string_detector_integrationtest.php',
            '--visitors' => 'Argument,Array,Assign,Condition,DefaultParameter,Operation,Property,Return,SwitchCase,Ternary',
            '--processors' => 'IgnoreArrayKey,IgnoreDefineFunction',

        ], $this->magicStringDetectorCommand->getDefinition());

        $output = new BufferedOutput();
        $exitCode = $this->magicStringDetectorCommand->run($input, $output);
        $outputText = str_replace('\n', '', $output->fetch());

        $this->assertSame(Application::EXIT_CODE_FAILURE, $exitCode);
        $this->assertStringStartsWith(
            '<command-start>Starting magic-string-detector command</command-start>',
            $outputText
        );
        $this->assertStringContainsString('Processors processed magic strings. Magic strings found:', $outputText);

        $expectedOutput = <<<EOT
+--------------------------------------------------------------+----------------------------------------------+
| String                                                       | Occurrence                                   |
+--------------------------------------------------------------+----------------------------------------------+
| return <focus>''</focus>                                     | magic_string_detector_integrationtest.php:20 |
|--------------------------------------------------------------|----------------------------------------------|
| strval(<focus>'10'</focus>)                                  | magic_string_detector_integrationtest.php:49 |
|--------------------------------------------------------------|----------------------------------------------|
| 'de'.<focus>'_'</focus>                                      | magic_string_detector_integrationtest.php:38 |
|--------------------------------------------------------------|----------------------------------------------|
| 1 === 0 ? <focus>'a'</focus> : 'b'                           | magic_string_detector_integrationtest.php:55 |
|--------------------------------------------------------------|----------------------------------------------|
| \$input.<focus>'additional'</focus>                           | magic_string_detector_integrationtest.php:35 |
|--------------------------------------------------------------|----------------------------------------------|
| 1 === 0 ? 'a' : <focus>'b'</focus>                           | magic_string_detector_integrationtest.php:55 |
|--------------------------------------------------------------|----------------------------------------------|
| private \$variable = <focus>'bar'</focus>                     | magic_string_detector_integrationtest.php:11 |
|--------------------------------------------------------------|----------------------------------------------|
| \$input !== <focus>'c++'</focus>                              | magic_string_detector_integrationtest.php:32 |
|--------------------------------------------------------------|----------------------------------------------|
| <focus>'de'</focus>.'_'                                      | magic_string_detector_integrationtest.php:38 |
|--------------------------------------------------------------|----------------------------------------------|
| 'de'.'_'.<focus>'DE'</focus>                                 | magic_string_detector_integrationtest.php:38 |
|--------------------------------------------------------------|----------------------------------------------|
| \$variable = <focus>'foo'</focus>                             | magic_string_detector_integrationtest.php:5  |
|--------------------------------------------------------------|----------------------------------------------|
| case <focus>'java'</focus>:                                  | magic_string_detector_integrationtest.php:19 |
|--------------------------------------------------------------|----------------------------------------------|
| 'name' => <focus>'Name'</focus>                              | magic_string_detector_integrationtest.php:24 |
|--------------------------------------------------------------|----------------------------------------------|
| \$input === <focus>'php'</focus>                              | magic_string_detector_integrationtest.php:14 |
|--------------------------------------------------------------|----------------------------------------------|
| \$input = <focus>'php'</focus>                                | magic_string_detector_integrationtest.php:13 |
|--------------------------------------------------------------|----------------------------------------------|
| return <focus>'php'</focus>                                  | magic_string_detector_integrationtest.php:15 |
|--------------------------------------------------------------|----------------------------------------------|
| return <focus>'string'</focus>                               | magic_string_detector_integrationtest.php:41 |
+--------------------------------------------------------------+----------------------------------------------+
EOT;
        $this->assertStringContainsString($expectedOutput, $outputText);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testRunWillGiveExitCode0WhenNoMagicStringsWereFound(): void
    {
        $input = new ArrayInput([
            'magic-string-detector',
            'directory' => __DIR__ . '/../../../../tests/data',
            '--include-files' => 'empty.php',
            '--visitors' => 'Argument,Array,Assign,Condition,DefaultParameter,Operation,Property,Return,SwitchCase',
            '--processors' => 'IgnoreArrayKey,IgnoreDefineFunction',

        ], $this->magicStringDetectorCommand->getDefinition());

        $output = new BufferedOutput();
        $exitCode = $this->magicStringDetectorCommand->run($input, $output);
        $outputText = $output->fetch();

        $this->assertSame(Application::EXIT_CODE_SUCCESS, $exitCode);
        $this->assertStringStartsWith(
            '<command-start>Starting magic-string-detector command</command-start>',
            $outputText
        );
    }
}

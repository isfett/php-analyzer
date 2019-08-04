<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Integration\Console;

use Isfett\PhpAnalyzer\Builder\ConditionListBuilder;
use Isfett\PhpAnalyzer\Builder\FinderBuilder;
use Isfett\PhpAnalyzer\Builder\ProcessorBuilder;
use Isfett\PhpAnalyzer\Builder\SortConfigurationBuilder;
use Isfett\PhpAnalyzer\Builder\VisitorBuilder;
use Isfett\PhpAnalyzer\Console\Application;
use Isfett\PhpAnalyzer\Console\Command\MostUsedConditionsCommand;
use Isfett\PhpAnalyzer\Node\ProcessorRunner;
use Isfett\PhpAnalyzer\Service\NodeRepresentationService;
use Isfett\PhpAnalyzer\Service\SortService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class MostUsedConditionsCommandTest
 */
class MostUsedConditionsCommandTest extends TestCase
{
    /** @var Application */
    private $application;

    /** @var MostUsedConditionsCommand */
    private $mostUsedConditionsCommand;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mostUsedConditionsCommand = new MostUsedConditionsCommand(
            new FinderBuilder(),
            new ConditionListBuilder(),
            new VisitorBuilder(),
            new ProcessorBuilder(),
            new SortConfigurationBuilder(),
            new ProcessorRunner(),
            new NodeRepresentationService(),
            new SortService()
        );

        $this->application = new Application();
        $this->application->addCommands([$this->mostUsedConditionsCommand]);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testRun(): void
    {
        $input = new ArrayInput([
            'most-used-conditions',
            'directory' => __DIR__.'/../../../../tests/Unit/data',
        ], $this->mostUsedConditionsCommand->getDefinition());

        $output = new BufferedOutput();
        $exitCode = $this->mostUsedConditionsCommand->run($input, $output);
        $outputText = $output->fetch();

        $this->assertEquals(Application::EXIT_CODE_SUCCESS, $exitCode);
        $this->assertStringStartsWith(
            '<command-start>Starting most-used-conditions command</command-start>',
            $outputText
        );
        $this->assertStringContainsString('<focus>', $outputText);
    }
}

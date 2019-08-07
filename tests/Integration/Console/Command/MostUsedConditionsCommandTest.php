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
            'directory' => __DIR__ . '/../../../../tests/data',
            '--include-files' => 'most_used_conditions_integrationtest.php',
            '--visitors' => 'If,ElseIf,Ternary,Coalesce,BooleanReturn',
            '--processors' => 'SplitIsset,SplitLogicalOperator,NegateBooleanNot,RemoveAssignment,RemoveCast,RemoveDuplicateBooleanNot,RemoveSingleFullyQualifiedName',
            '--with-flip-check' => true,

        ], $this->mostUsedConditionsCommand->getDefinition());

        $output = new BufferedOutput();
        $exitCode = $this->mostUsedConditionsCommand->run($input, $output);
        $outputText = $output->fetch();

        $this->assertEquals(Application::EXIT_CODE_SUCCESS, $exitCode);
        $this->assertStringStartsWith(
            '<command-start>Starting most-used-conditions command</command-start>',
            $outputText
        );

        $expectedOutput = <<<EOT
+-------------------------------------------------------------------+-------+
| Condition                                                         | Count |
+-------------------------------------------------------------------+-------+
| <focus>\$_GET['page']</focus>                                      | 1     |
| most_used_conditions_integrationtest.php:21 <flag></flag>         |       |
|-------------------------------------------------------------------|-------|
| <focus>\$i > 3</focus>                                             | 1     |
| most_used_conditions_integrationtest.php:13 <flag></flag>         |       |
|-------------------------------------------------------------------|-------|
| <focus>\$someVar</focus>                                           | 1     |
| most_used_conditions_integrationtest.php:11 <flag></flag>         |       |
|-------------------------------------------------------------------|-------|
| <focus>\$someVar !== 0</focus>                                     | 1     |
| most_used_conditions_integrationtest.php:11 <flag></flag>         |       |
|-------------------------------------------------------------------|-------|
| <focus>\$this->getUser()</focus>                                   | 1     |
| most_used_conditions_integrationtest.php:9 <flag></flag>          |       |
|-------------------------------------------------------------------|-------|
| <focus>\$x === \$y</focus>                                          | 1     |
| most_used_conditions_integrationtest.php:25-26 <flag></flag>      |       |
|-------------------------------------------------------------------|-------|
| <focus>30 !== date('d')</focus>                                   | 1     |
| most_used_conditions_integrationtest.php:15 <flag></flag>         |       |
|-------------------------------------------------------------------|-------|
| <focus>isset(\$_GET['page'])</focus>                               | 1     |
| most_used_conditions_integrationtest.php:19 <flag></flag>         |       |
|-------------------------------------------------------------------|-------|
| <focus>isset(\$_SESSION['user'])</focus>                           | 1     |
| most_used_conditions_integrationtest.php:3 <flag></flag>          |       |
|-------------------------------------------------------------------|-------|
| <focus>isset(\$_SESSION['user']['id'])</focus>                     | 1     |
| most_used_conditions_integrationtest.php:3 <flag></flag>          |       |
|-------------------------------------------------------------------|-------|
| <focus>strtolower('Chris') === 'chris'</focus>                    | 1     |
| most_used_conditions_integrationtest.php:17 <flag></flag>         |       |
|-------------------------------------------------------------------|-------|
| <focus>3771 === 1337</focus>                                      | 2     |
| most_used_conditions_integrationtest.php:7 <flag></flag>          |       |
| most_used_conditions_integrationtest.php:5 <flag>(flipped)</flag> |       |
|-------------------------------------------------------------------|-------|
| <focus>null === \$user</focus>                                     | 2     |
| most_used_conditions_integrationtest.php:5 <flag></flag>          |       |
| most_used_conditions_integrationtest.php:15 <flag></flag>         |       |
+-------------------------------------------------------------------+-------+
EOT;
        $this->assertStringContainsString($expectedOutput, $outputText);
    }
}

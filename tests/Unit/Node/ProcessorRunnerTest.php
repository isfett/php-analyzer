<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\Node\Processor\ProcessorInterface;
use Isfett\PhpAnalyzer\Node\ProcessorRunner;

/**
 * Class ProcessorRunnerTest
 */
class ProcessorRunnerTest extends AbstractNodeTestCase
{
    /** @var ProcessorRunner */
    private $processorRunner;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processorRunner = new ProcessorRunner();
    }

    /**
     * @return void
     */
    public function testProcess(): void
    {
        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($this->createOccurrence($this->createVariableNode('var1')));
        $nodeOccurrenceList->addOccurrence($this->createOccurrence($this->createVariableNode('var2')));
        $nodeOccurrenceList->addOccurrence($this->createOccurrence($this->createVariableNode('var3')));

        $childProcessor = $this->createMock(ProcessorInterface::class);
        $childProcessor
            ->expects($this->once())
            ->method('setNodeOccurrenceList')
            ->with($nodeOccurrenceList);
        $childProcessor
            ->expects($this->exactly(3))
            ->method('process');

        $this->processorRunner->addProcessor($childProcessor);

        $processorsDoneCounter = 1;
        foreach ($this->processorRunner->process($nodeOccurrenceList) as $processorsDone) {
            $this->assertEquals($processorsDoneCounter, $processorsDone);

            $processorsDoneCounter++;
        }
    }
}

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Processor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\Node\Processor\Condition\SplitLogicalOperatorProcessor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\LogicalAnd;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\BooleanNot;

/**
 * Class SplitLogicalOperatorProcessorTest
 */
class SplitLogicalOperatorProcessorTest extends AbstractNodeTestCase
{
    /** @var SplitLogicalOperatorProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new SplitLogicalOperatorProcessor();
    }

    /**
     * @return array
     */
    public function logicalOperatorProvider(): array
    {
        return [
            '&&' => [BooleanAnd::class],
            '||' => [BooleanOr::class],
            'and' => [LogicalAnd::class],
            'or' => [LogicalOr::class],
        ];
    }

    /**
     * @return void
     *
     * @dataProvider logicalOperatorProvider
     */
    public function testProcess(string $logicalProviderClass): void
    {
        $childNodes = [
            $this->createVariableNode('a'),
            $this->createVariableNode('b'),
        ];
        $node = new $logicalProviderClass($childNodes[0], $childNodes[1], $this->getNodeAttributes());

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(2, $nodeOccurrenceList->getOccurrences());

        foreach (array_values($nodeOccurrenceList->getOccurrences()->toArray()) as $key => $occurrence) {
            $this->assertEquals($childNodes[$key], $occurrence->getNode());
            $this->assertContains('SplitLogicalOperator', $occurrence->getAffectedByProcessors());
        }
    }

    /**
     * @return void
     */
    public function testProcessNested(): void
    {
        $childNodes = [
            $this->createVariableNode('a'),
            $this->createVariableNode('b'),
            $this->createVariableNode('c'),
            $this->createVariableNode('d'),
        ];
        $node = new BooleanAnd(
            new BooleanAnd(
                $childNodes[0],
                $childNodes[1],
                $this->getNodeAttributes()
            ),
            new BooleanOr(
                $childNodes[2],
                $childNodes[3],
                $this->getNodeAttributes()
            ),
            $this->getNodeAttributes()
        );

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(4, $nodeOccurrenceList->getOccurrences());

        foreach (array_values($nodeOccurrenceList->getOccurrences()->toArray()) as $key => $occurrence) {
            $this->assertEquals($childNodes[$key], $occurrence->getNode());
            $this->assertContains('SplitLogicalOperator', $occurrence->getAffectedByProcessors());
        }
    }

    /**
     * @return void
     */
    public function testProcessBooleanNot(): void
    {
        $childNodes = [
            $this->createVariableNode('a'),
            $this->createVariableNode('b'),
        ];
        $node = new BooleanNot(
            new BooleanAnd(
                $childNodes[0],
                $childNodes[1],
                $this->getNodeAttributes()
            ),
            $this->getNodeAttributes()
        );

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        $this->assertCount(2, $nodeOccurrenceList->getOccurrences());

        foreach (array_values($nodeOccurrenceList->getOccurrences()->toArray()) as $key => $occurrence) {
            $this->assertInstanceOf(BooleanNot::class, $occurrence->getNode());
            $this->assertEquals($childNodes[$key], $occurrence->getNode()->expr);
            $this->assertContains('SplitLogicalOperator', $occurrence->getAffectedByProcessors());
        }
    }

    /**
     * @return void
     */
    public function testProcessWillNotAffectedWrongNodes(): void
    {
        $node = $this->createVariableNode('a');
        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        /** @var Occurrence $occurrence */
        $occurrence = $nodeOccurrenceList->getOccurrences()->first();

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());
    }

    /**
     * @return void
     */
    public function testProcessWillNotAffectedWrongNodesWithBinaryOp(): void
    {
        $node = new Identical(
            $this->createVariableNode('a'),
            $this->createVariableNode('b'),
            $this->getNodeAttributes()
        );
        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new OccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());

        $this->processor->process($occurrence);

        /** @var Occurrence $occurrence */
        $occurrence = $nodeOccurrenceList->getOccurrences()->first();

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());
        $this->assertEmpty($occurrence->getAffectedByProcessors());
    }
}

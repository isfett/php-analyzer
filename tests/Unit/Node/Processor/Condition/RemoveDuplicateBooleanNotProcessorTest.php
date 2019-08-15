<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Processor\Condition;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Processor\Condition\RemoveDuplicateBooleanNotProcessor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;

/**
 * Class RemoveDuplicateBooleanNotProcessorTest
 */
class RemoveDuplicateBooleanNotProcessorTest extends AbstractNodeTestCase
{
    /** @var RemoveDuplicateBooleanNotProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new RemoveDuplicateBooleanNotProcessor();
    }

    /**
     * @return void
     */
    public function testProcess(): void
    {
        $node = new BooleanNot(
            new BooleanNot(
                new Identical(
                    $this->createVariableNode('x'),
                    $this->createVariableNode('y'),
                    $this->getNodeAttributes()
                ),
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

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $nodeOccurrenceList->getOccurrences()->first();

        $this->assertInstanceOf(Identical::class, $occurrence->getNode());
        $this->assertContains('RemoveDuplicateBooleanNot', $occurrence->getAffectedByProcessors());
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

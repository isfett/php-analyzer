<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Processor;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Processor\NegateBooleanNotProcessor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;

/**
 * Class NegateBooleanNotProcessorTest
 */
class NegateBooleanNotProcessorTest extends AbstractNodeTestCase
{
    /** @var NegateBooleanNotProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new NegateBooleanNotProcessor();
    }

    /**
     * @return void
     */
    public function testProcess(): void
    {
        $childNodes = [
            $this->createVariableNode('a'),
            $this->createVariableNode('b'),
        ];
        $node = new BooleanNot(
            new Identical(
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

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $nodeOccurrenceList->getOccurrences()->first();

        $this->assertInstanceOf(NotIdentical::class, $occurrence->getNode());
        $this->assertContains('NegateBooleanNot', $occurrence->getAffectedByProcessors());
    }

    /**
     * @return void
     */
    public function testProcessNested(): void
    {
        $childNodes = [
            $this->createVariableNode('a'),
            $this->createVariableNode('b'),
        ];
        $node = new BooleanNot(
            new BooleanAnd(
                new Identical(
                    $childNodes[0],
                    $childNodes[1],
                    $this->getNodeAttributes()
                ),
                new Identical(
                    $childNodes[0],
                    $childNodes[1],
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

        $this->assertInstanceOf(BooleanAnd::class, $occurrence->getNode());
        $this->assertInstanceOf(NotIdentical::class, $occurrence->getNode()->left);
        $this->assertInstanceOf(NotIdentical::class, $occurrence->getNode()->right);
        $this->assertContains('NegateBooleanNot', $occurrence->getAffectedByProcessors());
    }

    /**
     * @return void
     */
    public function testProcessNestedBooleanNot(): void
    {
        $childNodes = [
            $this->createVariableNode('a'),
            $this->createVariableNode('b'),
        ];
        $node = new BooleanNot(
            new BooleanAnd(
                new BooleanNot(
                    new Identical(
                        $childNodes[0],
                        $childNodes[1],
                        $this->getNodeAttributes()
                    ),
                    $this->getNodeAttributes()
                ),
                new Identical(
                    $childNodes[0],
                    $childNodes[1],
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

        $this->assertInstanceOf(BooleanAnd::class, $occurrence->getNode());
        $this->assertInstanceOf(Identical::class, $occurrence->getNode()->left);
        $this->assertInstanceOf(NotIdentical::class, $occurrence->getNode()->right);
        $this->assertContains('NegateBooleanNot', $occurrence->getAffectedByProcessors());
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

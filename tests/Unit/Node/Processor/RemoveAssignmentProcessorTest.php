<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Processor;

use Isfett\PhpAnalyzer\DAO\OccurrenceList;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Processor\RemoveAssignmentProcessor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Variable;

/**
 * Class RemoveAssignmentProcessorTest
 */
class RemoveAssignmentProcessorTest extends AbstractNodeTestCase
{
    /** @var RemoveAssignmentProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new RemoveAssignmentProcessor();
    }

    /**
     * @return void
     */
    public function testProcess(): void
    {
        $node = new Assign(
            $this->createVariableNode('x'),
            new Identical(
                $this->createVariableNode('a'),
                $this->createVariableNode('b'),
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
        $this->assertInstanceOf(Variable::class, $occurrence->getNode()->left);
        $this->assertInstanceOf(Variable::class, $occurrence->getNode()->right);
        $this->assertContains('RemoveAssignment', $occurrence->getAffectedByProcessors());
    }

    /**
     * @return void
     */
    public function testProcessBinaryOp(): void
    {
        $node = new Identical(
            new Assign(
                $this->createVariableNode('x'),
                new Identical(
                    $this->createVariableNode('a'),
                    $this->createVariableNode('b'),
                    $this->getNodeAttributes()
                ),
                $this->getNodeAttributes()
            ),
            new Variable(
                'y',
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
        $this->assertInstanceOf(Identical::class, $occurrence->getNode()->left);
        $this->assertInstanceOf(Variable::class, $occurrence->getNode()->left->left);
        $this->assertInstanceOf(Variable::class, $occurrence->getNode()->left->right);
        $this->assertContains('RemoveAssignment', $occurrence->getAffectedByProcessors());
    }

    /**
     * @return void
     */
    public function testProcessBinaryOpNested(): void
    {
        $node = new BooleanAnd(
            new Identical(
                new Assign(
                    $this->createVariableNode('x'),
                    new Identical(
                        $this->createVariableNode('a'),
                        $this->createVariableNode('b'),
                        $this->getNodeAttributes()
                    ),
                    $this->getNodeAttributes()
                ),
                new Variable(
                    'y',
                    $this->getNodeAttributes()
                ),
                $this->getNodeAttributes()
            ),
            new Identical(
                new Variable(
                    'x',
                    $this->getNodeAttributes()
                ),
                new Variable(
                    'x',
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
        $this->assertInstanceOf(Identical::class, $occurrence->getNode()->left->left);
        $this->assertInstanceOf(Variable::class, $occurrence->getNode()->left->left->left);
        $this->assertInstanceOf(Variable::class, $occurrence->getNode()->left->left->right);
        $this->assertContains('RemoveAssignment', $occurrence->getAffectedByProcessors());
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

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Processor;

use Isfett\PhpAnalyzer\DAO\NodeOccurrenceList;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Processor\RemoveSingleFullyQualifiedNamesProcessor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;

/**
 * Class RemoveSingleFullyQualifiedNamesProcessorTest
 */
class RemoveSingleFullyQualifiedNamesProcessorTest extends AbstractNodeTestCase
{
    /** @var RemoveSingleFullyQualifiedNamesProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new RemoveSingleFullyQualifiedNamesProcessor();
    }

    /**
     * @return void
     */
    public function testProcess(): void
    {
        $node = new ConstFetch(
            new FullyQualified(
                'test',
                $this->getNodeAttributes()
            ),
            $this->getNodeAttributes()
        );
        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new NodeOccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        $this->processor->process($occurrence);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $nodeOccurrenceList->getOccurrences()->first();

        $this->assertInstanceOf(ConstFetch::class, $occurrence->getNode());
        $this->assertInstanceOf(Name::class, $occurrence->getNode()->name);
        $this->assertEquals('test', (string) $occurrence->getNode()->name);
    }

    /**
     * @return void
     */
    public function testProcessBinaryOp(): void
    {
        $node = new Identical(
            new ConstFetch(
                new FullyQualified(
                    'test',
                    $this->getNodeAttributes()
                ),
                $this->getNodeAttributes()
            ),
            new Variable(
                'x',
                $this->getNodeAttributes()
            ),
            $this->getNodeAttributes()
        );

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new NodeOccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        $this->processor->process($occurrence);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $nodeOccurrenceList->getOccurrences()->first();

        $this->assertInstanceOf(Identical::class, $occurrence->getNode());
        $this->assertInstanceOf(ConstFetch::class, $occurrence->getNode()->left);
        $this->assertInstanceOf(Name::class, $occurrence->getNode()->left->name);
        $this->assertEquals('test', (string) $occurrence->getNode()->left->name);
    }

    /**
     * @return void
     */
    public function testProcessBinaryOpNested(): void
    {
        $node = new BooleanAnd(
            new Identical(
                new ConstFetch(
                    new FullyQualified(
                        'test',
                        $this->getNodeAttributes()
                    ),
                    $this->getNodeAttributes()
                ),
                new Variable(
                    'x',
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

        $nodeOccurrenceList = new NodeOccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        $this->processor->process($occurrence);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $nodeOccurrenceList->getOccurrences()->first();

        $this->assertInstanceOf(BooleanAnd::class, $occurrence->getNode());
        $this->assertInstanceOf(Identical::class, $occurrence->getNode()->left);
        $this->assertInstanceOf(ConstFetch::class, $occurrence->getNode()->left->left);
        $this->assertInstanceOf(Name::class, $occurrence->getNode()->left->left->name);
        $this->assertEquals('test', (string) $occurrence->getNode()->left->left->name);
    }
}

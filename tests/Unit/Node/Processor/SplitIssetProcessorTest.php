<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Processor;

use Isfett\PhpAnalyzer\DAO\NodeOccurrenceList;
use Isfett\PhpAnalyzer\Node\Processor\SplitIssetProcessor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\Isset_;

/**
 * Class SplitIssetProcessorTest
 */
class SplitIssetProcessorTest extends AbstractNodeTestCase
{
    /** @var SplitIssetProcessor */
    private $processor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = new SplitIssetProcessor();
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
        $node = new Isset_($childNodes, $this->getNodeAttributes());

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new NodeOccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        $this->processor->process($occurrence);

        $this->assertCount(2, $nodeOccurrenceList->getOccurrences());

        foreach (array_values($nodeOccurrenceList->getOccurrences()->toArray()) as $key => $occurrence) {
            $this->assertInstanceOf(Isset_::class, $occurrence->getNode());
            $this->assertCount(1, $occurrence->getNode()->vars);
            $this->assertEquals($childNodes[$key], $occurrence->getNode()->vars[0]);
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
        $node = new BooleanNot(new Isset_($childNodes, $this->getNodeAttributes()), $this->getNodeAttributes());

        $occurrence = $this->createOccurrence($node);

        $nodeOccurrenceList = new NodeOccurrenceList();
        $nodeOccurrenceList->addOccurrence($occurrence);
        $this->processor->setNodeOccurrenceList($nodeOccurrenceList);

        $this->assertCount(1, $nodeOccurrenceList->getOccurrences());

        $this->processor->process($occurrence);

        $this->assertCount(2, $nodeOccurrenceList->getOccurrences());

        foreach (array_values($nodeOccurrenceList->getOccurrences()->toArray()) as $key => $occurrence) {
            $this->assertInstanceOf(BooleanNot::class, $occurrence->getNode());
            $this->assertInstanceOf(Isset_::class, $occurrence->getNode()->expr);
            $this->assertCount(1, $occurrence->getNode()->expr->vars);
            $this->assertEquals($childNodes[$key], $occurrence->getNode()->expr->vars[0]);
        }
    }
}

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Visitor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Visitor\Condition\IfVisitor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class IfVisitorTest
 */
class IfVisitorTest extends AbstractNodeTestCase
{
    /** @var IfVisitor */
    private $visitor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new IfVisitor();

        /** @var MockObject|SplFileInfo $file */
        $file = $this->createSplFileInfoMock();

        $this->visitor->setFile($file);
    }

    /**
     * @return void
     */
    public function testEnterNode(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new If_(
            $this->createScalarStringNode('xxx'),
            [],
            $this->getNodeAttributes()
        );
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $this->visitor->getNodeOccurrenceList()->getOccurrences()->first();
        $this->assertSame($node->cond, $occurrence->getNode());
    }

    /**
     * @return void
     */
    public function testEnterNodeWithElseIfs(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new If_(
            $this->createScalarStringNode('0'),
            [
                'elseifs' => [
                    new ElseIf_($this->createScalarStringNode('1'), $this->getNodeAttributes()),
                    new ElseIf_($this->createScalarStringNode('2'), $this->getNodeAttributes()),
                ],
            ],
            $this->getNodeAttributes()
        );
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        /**
         * @var int $key
         * @var Occurrence $occurrence
         */
        foreach ($this->visitor->getNodeOccurrenceList()->getOccurrences() as $key => $occurrence) {
            /** @var String_ $node */
            $node = $occurrence->getNode();
            $this->assertEquals($key, $node->value);
        }
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddWrongNodes(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new Ternary(
            $this->createVariableNode('x'),
            $this->createVariableNode('y'),
            $this->createVariableNode('z')
        );
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }
}

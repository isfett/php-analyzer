<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Visitor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Visitor\Condition\CoalesceVisitor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Stmt\If_;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class CoalesceVisitorTest
 */
class CoalesceVisitorTest extends AbstractNodeTestCase
{
    /** @var CoalesceVisitor */
    private $visitor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new CoalesceVisitor();

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

        $node = new Coalesce($this->createVariableNode('x'), $this->createVariableNode('y'));
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $this->visitor->getNodeOccurrenceList()->getOccurrences()->first();
        $this->assertSame($node->left, $occurrence->getNode());
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddWrongNodes(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new If_($this->createScalarStringNode('xxx'), [], $this->getNodeAttributes());
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }
}

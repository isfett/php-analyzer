<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Visitor\Condition;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Visitor\Condition\BooleanReturnVisitor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class BooleanReturnVisitorTest
 */
class BooleanReturnVisitorTest extends AbstractNodeTestCase
{
    /** @var BooleanReturnVisitor */
    private $visitor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new BooleanReturnVisitor();

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

        $expr = new Identical(
            $this->createVariableNode('x'),
            $this->createScalarStringNode('foo'),
            $this->getNodeAttributes()
        );

        $stmts = [
            new Return_(
                $expr,
                $this->getNodeAttributes()
            ),
        ];

        $node = new Function_(
            $this->createIdentifierNode('test'),
            [
                'stmts' => $stmts,
                'returnType' => $this->createIdentifierNode('bool'),
            ],
            $this->getNodeAttributes()
        );
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        /** @var Occurrence $occurrence */
        $occurrence = $this->visitor->getNodeOccurrenceList()->getOccurrences()->first();
        $this->assertSame($expr, $occurrence->getNode());
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddWrongNodes(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        // visitor just should add :bool functions, no coalesce
        $node = new Coalesce($this->createVariableNode('x'), $this->createVariableNode('y'));
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddFunctionsWithoutReturnTypeHint(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $expr = new Identical(
            $this->createVariableNode('x'),
            $this->createScalarStringNode('foo'),
            $this->getNodeAttributes()
        );

        $stmts = [
            new Return_(
                $expr,
                $this->getNodeAttributes()
            ),
        ];

        $node = new Function_(
            $this->createIdentifierNode('test'),
            [
                'stmts' => $stmts,
                'returnType' => null,
            ],
            $this->getNodeAttributes()
        );
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddFunctionsWithWrongReturnTypeHint(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $expr = new Identical(
            $this->createVariableNode('x'),
            $this->createScalarStringNode('foo'),
            $this->getNodeAttributes()
        );

        $stmts = [
            new Return_(
                $expr,
                $this->getNodeAttributes()
            ),
        ];

        $node = new Function_(
            $this->createIdentifierNode('test'),
            [
                'stmts' => $stmts,
                'returnType' => $this->createIdentifierNode('string'),
            ],
            $this->getNodeAttributes()
        );

        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }
}

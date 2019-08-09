<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Visitor;

use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Visitor\BooleanReturnConditionVisitor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Return_;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class BooleanReturnConditionVisitorTest
 */
class BooleanReturnConditionVisitorTest extends AbstractNodeTestCase
{
    /** @var BooleanReturnConditionVisitor */
    private $visitor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new BooleanReturnConditionVisitor();

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

        // visitor just should add :bool functions, no coalesce
        $node = new Coalesce($this->createVariableNode('x'), $this->createVariableNode('y'));
        $this->visitor->enterNode($node);
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

        // function without return type declaration (:bool)
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

        // function with wrong return type declaration (here string)
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

        // this should be added
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
}

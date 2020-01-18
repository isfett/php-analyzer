<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Visitor\MagicNumber;

use Isfett\PhpAnalyzer\Node\Visitor\MagicNumber\TernaryVisitor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Scalar\LNumber;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class TernaryVisitorTest
 */
class TernaryVisitorTest extends AbstractNodeTestCase
{
    /** @var TernaryVisitor */
    private $visitor;

    /**
     * @return void
     */
    public function testEnterNode(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new Ternary(
            new Identical(
                $this->createVariableNode('a'),
                $this->createVariableNode('b')
            ),
            $this->createLNumberNode(1337),
            $this->createLNumberNode(1338)
        );

        $this->visitor->enterNode($node);
        $this->assertCount(2, $this->visitor->getNodeOccurrenceList()->getOccurrences());
        $this->assertSame(1337, $this->visitor->getNodeOccurrenceList()->getOccurrences()->first()->getNode()->value);
        $this->assertSame(1338, $this->visitor->getNodeOccurrenceList()->getOccurrences()->last()->getNode()->value);
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddNonNumbers(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new Ternary(
            new Identical(
                $this->createVariableNode('a'),
                $this->createVariableNode('b')
            ),
            $this->createScalarStringNode('1337'),
            $this->createScalarStringNode('1338')
        );
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddNullValues(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new Ternary(
            new Identical(
                $this->createVariableNode('a'),
                $this->createVariableNode('b')
            ),
            null,
            $this->createLNumberNode(1338)
        );
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());
        $this->assertSame(1338, $this->visitor->getNodeOccurrenceList()->getOccurrences()->last()->getNode()->value);
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddWrongNodes(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new LNumber(
            1,
            $this->getNodeAttributes(
                1,
                1,
                new Smaller(
                    $this->createLNumberNode(1),
                    $this->createLNumberNode(2)
                )
            )
        );
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new TernaryVisitor();

        /** @var MockObject|SplFileInfo $file */
        $file = $this->createSplFileInfoMock();

        $this->visitor->setFile($file);
    }
}

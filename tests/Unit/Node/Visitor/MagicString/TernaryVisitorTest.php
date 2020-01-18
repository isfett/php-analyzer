<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Visitor\MagicString;

use Isfett\PhpAnalyzer\Node\Visitor\MagicString\TernaryVisitor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
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
            $this->createScalarStringNode('foo'),
            $this->createScalarStringNode('bar')
        );

        $this->visitor->enterNode($node);
        $this->assertCount(2, $this->visitor->getNodeOccurrenceList()->getOccurrences());
        $this->assertSame('foo', $this->visitor->getNodeOccurrenceList()->getOccurrences()->first()->getNode()->value);
        $this->assertSame('bar', $this->visitor->getNodeOccurrenceList()->getOccurrences()->last()->getNode()->value);
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddNonStrings(): void
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
            $this->createScalarStringNode('foo')
        );
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());
        $this->assertSame('foo', $this->visitor->getNodeOccurrenceList()->getOccurrences()->last()->getNode()->value);
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddWrongNodes(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new String_(
            'foo',
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

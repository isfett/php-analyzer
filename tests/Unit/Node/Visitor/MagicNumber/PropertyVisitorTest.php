<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Visitor\MagicNumber;

use Isfett\PhpAnalyzer\Node\Visitor\MagicNumber\PropertyVisitor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\PropertyProperty;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class PropertyVisitorTest
 */
class PropertyVisitorTest extends AbstractNodeTestCase
{
    /** @var PropertyVisitor */
    private $visitor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new PropertyVisitor();

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

        $node = new LNumber(
            1337,
            $this->getNodeAttributes(
                1,
                1,
                new PropertyProperty(
                    $this->createLNumberNode(1337)
                )
            )
        );
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());
        $this->assertEquals(1337, $this->visitor->getNodeOccurrenceList()->getOccurrences()->last()->getNode()->value);
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
    public function testEnterNodeWillNotAddNonNumbers(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new String_(
            'foo',
            $this->getNodeAttributes(
                1,
                1,
                new PropertyProperty(
                    $this->createScalarStringNode('foo')
                )
            )
        );
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }
}

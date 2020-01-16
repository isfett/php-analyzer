<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Visitor\MagicString;

use Isfett\PhpAnalyzer\Node\Visitor\MagicString\ConditionVisitor;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Case_;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ConditionVisitorTest
 */
class ConditionVisitorTest extends AbstractNodeTestCase
{
    /** @var ConditionVisitor */
    private $visitor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new ConditionVisitor();

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

        $node = new String_(
            'test',
            $this->getNodeAttributes(
                1,
                1,
                new SmallerOrEqual(
                    $this->createScalarStringNode('test'),
                    $this->createScalarStringNode('test2')
                )
            )
        );
        $this->visitor->enterNode($node);
        $this->assertCount(1, $this->visitor->getNodeOccurrenceList()->getOccurrences());
        $this->assertSame('test', $this->visitor->getNodeOccurrenceList()->getOccurrences()->last()->getNode()->value);
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddWrongNodes(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new String_(
            'test',
            $this->getNodeAttributes(
                1,
                1,
                new Case_(new String_('test'))
            )
        );
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }

    /**
     * @return void
     */
    public function testEnterNodeWillNotAddNonStrings(): void
    {
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());

        $node = new LNumber(
            1,
            $this->getNodeAttributes(
                1,
                1,
                new SmallerOrEqual(
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
    public function testEnterNodeWillNotAddNodesWhenComparingAgainstPhpDefinedConstants(): void
    {
        $node = new String_(
            'test',
            $this->getNodeAttributes(
                1,
                1,
                new SmallerOrEqual(
                    $this->createScalarStringNode('test'),
                    new ConstFetch($this->createNameNode('PHP_EXTRA_VERSION'))
                )
            )
        );
        $this->visitor->enterNode($node);
        $this->assertCount(0, $this->visitor->getNodeOccurrenceList()->getOccurrences());
    }
}

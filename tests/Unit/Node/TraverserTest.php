<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node;

use Isfett\PhpAnalyzer\DAO\NodeOccurrenceList;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use Isfett\PhpAnalyzer\Node\Traverser;
use Isfett\PhpAnalyzer\Node\AbstractVisitor;
use Isfett\PhpAnalyzer\Node\Visitor\VisitorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class TraverserTest
 */
class TraverserTest extends AbstractNodeTestCase
{
    /** @var Traverser */
    private $traverser;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->traverser = new Traverser();
    }

    /**
     * @return void
     */
    public function testUpdateFileInVisitor(): void
    {
        /** @var MockObject|SplFileInfo $file */
        $file = $this->createSplFileInfoMock();

        /** @var MockObject|VisitorInterface $visitor */
        $visitor = $this->createMock(AbstractVisitor::class);
        $visitor
            ->expects($this->once())
            ->method('setFile')
            ->with($file);

        $this->traverser->addVisitor($visitor);
        $this->traverser->setFile($file);
    }

    /**
     * @return void
     */
    public function testGetNodeOccurrencesCount(): void
    {
        /** @var MockObject|SplFileInfo $file */
        $file = $this->createSplFileInfoMock();

        $node = $this->createVariableNode();
        $nodeOccurrenceList = new NodeOccurrenceList();
        $nodeOccurrenceList->addOccurrence(new Occurrence($node, $file));
        $nodeOccurrenceList->addOccurrence(new Occurrence($node, $file));
        $nodeOccurrenceList->addOccurrence(new Occurrence($node, $file));
        /** @var MockObject|VisitorInterface $visitor */
        $visitor = $this->createMock(AbstractVisitor::class);
        $visitor
            ->method('getNodeOccurrenceList')
            ->willReturn($nodeOccurrenceList);

        $this->traverser->addVisitor($visitor);

        $this->assertEquals(3, $this->traverser->getNodeOccurrencesCount());
    }
}

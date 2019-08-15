<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\VisitorConnector;

use Isfett\PhpAnalyzer\Node\VisitorConnector\ParentConnector;
use Isfett\PhpAnalyzer\Tests\Unit\Node\AbstractNodeTestCase;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\LNumber;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ParentConnectorTest
 */
class ParentConnectorTest extends AbstractNodeTestCase
{
    /** @var ParentConnector */
    private $visitor;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visitor = new ParentConnector();

        /** @var MockObject|SplFileInfo $file */
        $file = $this->createSplFileInfoMock();

        $this->visitor->setFile($file);
    }

    /**
     * @return void
     */
    public function testEnterNode(): void
    {
        $numberNode = new LNumber(
            1,
            $this->getNodeAttributes()
        );

        $arrayItemNode = new ArrayItem(
            $numberNode,
            null,
            false,
            $this->getNodeAttributes()
        );

        $arrayNode = new Array_(
            [
                $arrayItemNode,
            ],
            $this->getNodeAttributes()
        );

        $this->visitor->beginTraverse([$arrayNode, $arrayItemNode, $numberNode]);
        $this->visitor->enterNode($arrayNode);
        $this->visitor->enterNode($arrayItemNode);
        $this->visitor->enterNode($numberNode);
        $this->visitor->leaveNode($numberNode);
        $this->visitor->leaveNode($arrayItemNode);
        $this->visitor->leaveNode($arrayNode);

        $this->assertInstanceOf(ArrayItem::class, $numberNode->getAttribute('parent'));
        $this->assertInstanceOf(Array_::class, $arrayItemNode->getAttribute('parent'));
        $this->assertNotContains('parent', $arrayNode->getAttributes());
    }
}

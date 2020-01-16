<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Stmt;

use Isfett\PhpAnalyzer\Node\Representation\Stmt\Return_;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class Return_Test
 */
class Return_Test extends AbstractNodeRepresentationTest
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testGetRepresentation(): void
    {
        $node = new Node\Stmt\Return_(
            $this->createLNumberNode(1),
            $this->getNodeAttributes()
        );

        $representation = new Return_($this->nodeRepresentationService, $node);

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('1');

        $this->assertSame('return 1', $representation->representation());
    }
}

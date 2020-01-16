<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\PostInc;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class PostIncTest
 */
class PostIncTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\PostInc(
            $this->createVariableNode('x'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$x');

        $representation = new PostInc($this->nodeRepresentationService, $node);

        $this->assertSame('$x++', $representation->representation());
    }
}

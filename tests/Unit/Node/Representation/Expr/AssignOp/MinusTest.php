<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr\AssignOp;

use Isfett\PhpAnalyzer\Node\Representation\Expr\AssignOp\Minus;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class MinusTest
 */
class MinusTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\AssignOp\Minus(
            $this->createVariableNode('variable'),
            $this->createVariableNode('variable2'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$variable', '$variable2');

        $representation = new Minus($this->nodeRepresentationService, $node);

        $this->assertSame('$variable -= $variable2', $representation->representation());
    }
}

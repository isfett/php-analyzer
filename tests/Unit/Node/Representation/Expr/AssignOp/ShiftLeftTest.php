<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr\AssignOp;

use Isfett\PhpAnalyzer\Node\Representation\Expr\AssignOp\ShiftLeft;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ShiftLeftTest
 */
class ShiftLeftTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\AssignOp\ShiftLeft(
            $this->createVariableNode('variable'),
            $this->createVariableNode('variable2'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$variable', '$variable2');

        $representation = new ShiftLeft($this->nodeRepresentationService, $node);

        $this->assertSame('$variable <<= $variable2', $representation->representation());
    }
}

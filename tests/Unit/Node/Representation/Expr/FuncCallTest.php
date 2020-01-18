<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\FuncCall;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class FuncCallTest
 */
class FuncCallTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\FuncCall(
            $this->createVariableNode('strtolower'),
            [
                $this->createArgNode($this->createVariableNode('x')),
            ],
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('strtolower');
        $this->nodeRepresentationService
            ->method('representationForArguments')
            ->willReturn(['$x']);

        $representation = new FuncCall($this->nodeRepresentationService, $node);

        $this->assertSame('strtolower($x)', $representation->representation());
    }
}

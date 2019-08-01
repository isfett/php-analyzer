<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\MethodCall;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class MethodCallTest
 */
class MethodCallTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\MethodCall(
            $this->createVariableNode('classObject'),
            $this->createNameNode('test'),
            [
                $this->createArgNode($this->createVariableNode('x')),
            ],
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$classObject', 'test');
        $this->nodeRepresentationService
            ->method('representationForArguments')
            ->willReturn(['$x']);

        $representation = new MethodCall($this->nodeRepresentationService, $node);

        $this->assertEquals('$classObject->test($x)', $representation->representation());
    }
}

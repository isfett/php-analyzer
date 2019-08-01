<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\StaticCall;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class StaticCallTest
 */
class StaticCallTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\StaticCall(
            $this->createNameNode('Classname'),
            'x',
            [
                $this->createArgNode($this->createVariableNode('x')),
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getRepresentationForNode')
            ->willReturn('Classname', 'x');
        $this->representation
            ->method('getArguments')
            ->willReturn(['$x']);

        $representation = new StaticCall($this->representation, $node);

        $this->assertEquals('Classname::x($x)', $representation->getRepresentation());
    }
}

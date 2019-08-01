<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\BooleanNot;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class BooleanNotTest
 */
class BooleanNotTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\ErrorSuppress(
            $this->createVariableNode('x'),
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getRepresentationForNode')
            ->willReturn('$x');

        $representation = new BooleanNot($this->representation, $node);

        $this->assertEquals('!$x', $representation->getRepresentation());
    }
}
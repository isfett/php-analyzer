<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\ClosureUse;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ClosureUseTest
 */
class ClosureUseTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\ClosureUse(
            $this->createVariableNode('x'),
            false,
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$x');

        $representation = new ClosureUse($this->nodeRepresentationService, $node);

        $this->assertEquals('$x', $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationByRef(): void
    {
        $node = new Node\Expr\ClosureUse(
            $this->createVariableNode('x'),
            true,
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$x');

        $representation = new ClosureUse($this->nodeRepresentationService, $node);

        $this->assertEquals('&$x', $representation->representation());
    }
}

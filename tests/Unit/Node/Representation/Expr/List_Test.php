<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\List_;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class List_Test
 */
class List_Test extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\List_(
            [
                $this->createArgNode($this->createVariableNode('x')),
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getArguments')
            ->willReturn(['$x']);

        $representation = new List_($this->representation, $node);

        $this->assertEquals('list($x)', $representation->getRepresentation());
    }
}

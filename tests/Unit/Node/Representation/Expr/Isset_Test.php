<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Isset_;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class Isset_Test
 */
class Isset_Test extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\Isset_(
            [
                $this->createVariableNode('x'),
            ],
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForArguments')
            ->willReturn(['$x']);

        $representation = new Isset_($this->nodeRepresentationService, $node);

        $this->assertSame('isset($x)', $representation->representation());
    }
}

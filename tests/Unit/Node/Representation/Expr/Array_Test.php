<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Array_;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class Array_Test
 */
class Array_Test extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\Array_(
            [
                $this->createVariableNode('test'),
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getArguments')
            ->willReturn(['$test']);

        $representation = new Array_($this->representation, $node);

        $this->assertEquals('[$test]', $representation->getRepresentation());
    }
}

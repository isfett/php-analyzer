<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Scalar;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Variable;
use Isfett\PhpAnalyzer\Node\Representation\Scalar\Encapsed;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class EncapsedTest
 */
class EncapsedTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Scalar\Encapsed(
            [
                $this->createVariableNode('x'),
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getArguments')
            ->willReturn(['$x']);

        $representation = new Encapsed($this->representation, $node);

        $this->assertEquals('"$x"', $representation->getRepresentation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationMultiple(): void
    {
        $node = new Node\Scalar\Encapsed(
            [
                new Node\Scalar\EncapsedStringPart('\\', $this->getNodeAttributes()),
                $this->createVariableNode('x'),
            ],
            $this->getNodeAttributes()
        );

        $this->representation
            ->method('getArguments')
            ->willReturn(['\\', '$x']);

        $representation = new Encapsed($this->representation, $node);

        $this->assertEquals('"\\$x"', $representation->getRepresentation());
    }
}

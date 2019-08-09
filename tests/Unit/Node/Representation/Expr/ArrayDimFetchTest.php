<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\ArrayDimFetch;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class ArrayDimFetchTest
 */
class ArrayDimFetchTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\ArrayDimFetch(
            $this->createVariableNode('test'),
            $this->createScalarStringNode('x'),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$test', "'x'");

        $representation = new ArrayDimFetch($this->nodeRepresentationService, $node);

        $this->assertEquals("\$test['x']", $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationWithEmptyDim(): void
    {
        $node = new Node\Expr\ArrayDimFetch(
            $this->createVariableNode('test'),
            null,
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$test');

        $representation = new ArrayDimFetch($this->nodeRepresentationService, $node);

        $this->assertEquals('$test[]', $representation->representation());
    }
}

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Stmt;

use Isfett\PhpAnalyzer\Node\Representation\Stmt\PropertyProperty;
use Isfett\PhpAnalyzer\Node\Representation\Stmt\Return_;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class PropertyPropertyTest
 */
class PropertyPropertyTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Stmt\PropertyProperty(
            'test',
            null,
            $this->getNodeAttributes()
        );

        $representation = new PropertyProperty($this->nodeRepresentationService, $node);

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$test');

        $this->assertEquals('$test', $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationWithDefaultParameter(): void
    {
        $node = new Node\Stmt\PropertyProperty(
            'test',
            new Node\Scalar\LNumber(1),
            $this->getNodeAttributes()
        );

        $representation = new PropertyProperty($this->nodeRepresentationService, $node);

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$test', 1);

        $this->assertEquals('$test = 1', $representation->representation());
    }
}

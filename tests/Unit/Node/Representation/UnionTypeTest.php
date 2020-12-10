<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation;

use Isfett\PhpAnalyzer\Node\Representation\UnionType;
use PhpParser\Node;

/**
 * Class UnionTypeTest
 */
class UnionTypeTest extends AbstractNodeRepresentationTest
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
        $node = new Node\UnionType([
            new Node\Identifier('string'),
            new Node\Name('int'),
        ], $this->getNodeAttributes());

        $this->nodeRepresentationService
            ->method('representationForArguments')
            ->willReturnOnConsecutiveCalls(['string', 'int']);

        $representation = new UnionType($this->nodeRepresentationService, $node);

        $this->assertSame('string|int', $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationSingle(): void
    {
        $node = new Node\UnionType([
            new Node\Identifier('string'),
        ], $this->getNodeAttributes());

        $this->nodeRepresentationService
            ->method('representationForArguments')
            ->willReturnOnConsecutiveCalls(['string']);

        $representation = new UnionType($this->nodeRepresentationService, $node);

        $this->assertSame('string', $representation->representation());
    }
}

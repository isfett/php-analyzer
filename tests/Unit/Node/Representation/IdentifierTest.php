<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation;

use Isfett\PhpAnalyzer\Node\Representation\Identifier;
use PhpParser\Node;

/**
 * Class IdentifierTest
 */
class IdentifierTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Identifier(
            'test',
            $this->getNodeAttributes()
        );

        $representation = new Identifier($this->nodeRepresentationService, $node);

        $this->assertEquals('test', $representation->representation());
    }
}

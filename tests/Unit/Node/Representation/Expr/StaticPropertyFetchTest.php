<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\StaticPropertyFetch;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class StaticPropertyFetchTest
 */
class StaticPropertyFetchTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\StaticPropertyFetch(
            $this->createNameNode('Classname'),
            'X',
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('Classname', 'X');

        $representation = new StaticPropertyFetch($this->nodeRepresentationService, $node);

        $this->assertEquals('Classname::X', $representation->representation());
    }
}

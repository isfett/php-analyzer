<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Variable;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class VariableTest
 */
class VariableTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\Variable(
            'test',
            $this->getNodeAttributes()
        );

        $representation = new Variable($this->nodeRepresentationService, $node);

        $this->assertSame('$test', $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationWithoutString(): void
    {
        $node = new Node\Expr\Variable( // $$test
            new Node\Expr\Variable(
                'test',
                $this->getNodeAttributes()
            ),
            $this->getNodeAttributes()
        );

        $representation = new Variable($this->nodeRepresentationService, $node);

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('$test');

        $this->assertSame('$$test', $representation->representation());
    }
}

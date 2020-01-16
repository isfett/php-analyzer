<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Exit_;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class Exit_Test
 */
class Exit_Test extends AbstractNodeRepresentationTest
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
        $node = new Node\Expr\Exit_(
            new Node\Scalar\LNumber(1, $this->getNodeAttributes()),
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('1');

        $representation = new Exit_($this->nodeRepresentationService, $node);

        $this->assertSame('exit(1)', $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationWithoutExpr(): void
    {
        $node = new Node\Expr\Exit_(
            null,
            $this->getNodeAttributes()
        );

        $representation = new Exit_($this->nodeRepresentationService, $node);

        $this->assertSame('exit', $representation->representation());
    }
}

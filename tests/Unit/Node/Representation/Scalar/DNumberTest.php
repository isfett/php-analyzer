<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Scalar;

use Isfett\PhpAnalyzer\Node\Representation\Scalar\DNumber;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class DNumberTest
 */
class DNumberTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Scalar\DNumber(
            1.337,
            $this->getNodeAttributes()
        );

        $representation = new DNumber($this->nodeRepresentationService, $node);

        $this->assertSame('1.337', $representation->representation());
    }
}

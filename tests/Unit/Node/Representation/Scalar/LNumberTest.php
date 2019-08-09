<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Scalar;

use Isfett\PhpAnalyzer\Node\Representation\Scalar\LNumber;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class LNumberTest
 */
class LNumberTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Scalar\LNumber(
            1337,
            $this->getNodeAttributes()
        );

        $representation = new LNumber($this->nodeRepresentationService, $node);

        $this->assertEquals('1337', $representation->representation());
    }
}

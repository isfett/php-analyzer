<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation;

use Isfett\PhpAnalyzer\Node\Representation\Name;
use PhpParser\Node;

/**
 * Class NameTest
 */
class NameTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Name(
            'test',
            $this->getNodeAttributes()
        );

        $representation = new Name($this->nodeRepresentationService, $node);

        $this->assertSame('test', $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationMultiple(): void
    {
        $node = new Node\Name(
            [
                'test',
                'test2',
            ],
            $this->getNodeAttributes()
        );

        $representation = new Name($this->nodeRepresentationService, $node);

        $this->assertSame('test\\test2', $representation->representation());
    }
}

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Name;

use Isfett\PhpAnalyzer\Node\Representation\Name\FullyQualified;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class FullyQualifiedTest
 */
class FullyQualifiedTest extends AbstractNodeRepresentationTest
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
        $node = new Node\Name\FullyQualified(
            'Exception',
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('Exception');

        $representation = new FullyQualified($this->nodeRepresentationService, $node);

        $this->assertEquals('\\Exception', $representation->representation());
    }

    /**
     * @return void
     */
    public function testGetRepresentationWithNamespacedClassname(): void
    {
        $node = new Node\Name\FullyQualified(
            [
                'MyNamespace',
                'Exception',
            ],
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn('MyNamespace', 'Exception');

        $representation = new FullyQualified($this->nodeRepresentationService, $node);

        $this->assertEquals('\\MyNamespace\\Exception', $representation->representation());
    }
}

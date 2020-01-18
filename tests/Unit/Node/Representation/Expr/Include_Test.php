<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\Expr\Include_;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class Include_Test
 */
class Include_Test extends AbstractNodeRepresentationTest
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return array
     */
    public function includeProvider(): array
    {
        return [
            'include' => [Node\Expr\Include_::TYPE_INCLUDE, 'include'],
            'include_once' => [Node\Expr\Include_::TYPE_INCLUDE_ONCE, 'include_once'],
            'require' => [Node\Expr\Include_::TYPE_REQUIRE, 'require'],
            'require_once' => [Node\Expr\Include_::TYPE_REQUIRE_ONCE, 'require_once'],
        ];
    }

    /**
     * @param int    $type
     * @param string $includeFunction
     *
     * @return void
     *
     * @dataProvider includeProvider
     */
    public function testGetRepresentation(int $type, string $includeFunction): void
    {
        $node = new Node\Expr\Include_(
            new Node\Scalar\String_('path/to/file'),
            $type,
            $this->getNodeAttributes()
        );

        $this->nodeRepresentationService
            ->method('representationForNode')
            ->willReturn("'path/to/file'");

        $representation = new Include_($this->nodeRepresentationService, $node);

        $this->assertSame(sprintf("%s('path/to/file')", $includeFunction), $representation->representation());
    }
}

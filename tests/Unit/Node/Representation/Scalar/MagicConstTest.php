<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Scalar;

use Isfett\PhpAnalyzer\Node\Representation\Scalar\MagicConst;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;

/**
 * Class MagicConstTest
 */
class MagicConstTest extends AbstractNodeRepresentationTest
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
    public function magicConstProvider(): array
    {
        return [
            'class' => ['__CLASS__', Node\Scalar\MagicConst\Class_::class],
            'dir' => ['__DIR__', Node\Scalar\MagicConst\Dir::class],
            'file' => ['__FILE__', Node\Scalar\MagicConst\File::class],
            'function' => ['__FUNCTION__', Node\Scalar\MagicConst\Function_::class],
            'line' => ['__LINE__', Node\Scalar\MagicConst\Line::class],
            'method' => ['__METHOD__', Node\Scalar\MagicConst\Method::class],
            'namespace' => ['__NAMESPACE__', Node\Scalar\MagicConst\Namespace_::class],
            'trait' => ['__TRAIT__', Node\Scalar\MagicConst\Trait_::class],
        ];
    }

    /**
     * @param string $expectedOutput
     * @param string $nodeClassname
     *
     * @return void
     *
     * @dataProvider magicConstProvider
     */
    public function testGetRepresentation(string $expectedOutput, string $nodeClassname): void
    {
        $node = new $nodeClassname(
            $this->getNodeAttributes()
        );

        $representation = new MagicConst($this->representation, $node);

        $this->assertEquals($expectedOutput, $representation->getRepresentation());
    }
}

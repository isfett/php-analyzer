<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\Stmt;

use Isfett\PhpAnalyzer\Node\Representation\Stmt\Property;
use Isfett\PhpAnalyzer\Tests\Unit\Node\Representation\AbstractNodeRepresentationTest;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

/**
 * Class PropertyTest
 */
class PropertyTest extends AbstractNodeRepresentationTest
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
    public function presentationProvider(): array
    {
        return [
            'without modifier' => ['public $test', 0, false, null], // default is public, when no access modifier is given
            'private' => ['private $test', Class_::MODIFIER_PRIVATE, false, null],
            'protected' => ['protected $test', Class_::MODIFIER_PROTECTED, false, null],
            'public' => ['public $test', Class_::MODIFIER_PUBLIC, false, null],
            'private static' => ['private static $test', Class_::MODIFIER_PRIVATE, true, null],
            'protected static' => ['protected static $test', Class_::MODIFIER_PROTECTED, true, null],
            'public static' => ['public static $test', Class_::MODIFIER_PUBLIC, true, null],
            'private type string' => ['private int $test', Class_::MODIFIER_PRIVATE, false, 'int'],
            'private type Identifier' => ['private int $test', Class_::MODIFIER_PRIVATE, false, new Node\Identifier('int')],
            'private type Name' => ['private int $test', Class_::MODIFIER_PRIVATE, false, new Node\Name('int')],
            'private type NullableType' => ['private ?int $test', Class_::MODIFIER_PRIVATE, false, new Node\NullableType (new Node\Identifier('int'))],
            'private type UnionType' => ['private int|string $test', Class_::MODIFIER_PRIVATE, false, new Node\UnionType ([new Node\Name('int'), new Node\Name('string')])],
        ];
    }

    /**
     * @param string $expectedOutput
     * @param int    $accessModifier
     * @param bool   $static
     * @param mixed  $type
     *
     * @return void
     *
     * @dataProvider presentationProvider
     */
    public function testGetPresentation(string $expectedOutput, int $accessModifier, bool $static, $type): void
    {
        $flags = $accessModifier + ($static ? Class_::MODIFIER_STATIC : 0);
        $propertyPropertyNode = new Node\Stmt\PropertyProperty('test');
        $node = new Node\Stmt\Property($flags, [$propertyPropertyNode], [], $type);

        $representation = new Property($this->nodeRepresentationService, $node);

        if (null !== $type) {
            if ($type instanceof Node\NullableType) {
                $this->nodeRepresentationService
                    ->method('representationForNode')
                    ->willReturn('?'.$type->type);
            } elseif ($type instanceof Node\UnionType) {
                $this->nodeRepresentationService
                    ->method('representationForNode')
                    ->willReturn(current($type->types).'|'.next($type->types));
            } else {
                $this->nodeRepresentationService
                    ->method('representationForNode')
                    ->willReturn($type);
            }
        }

        $this->nodeRepresentationService
            ->method('representationForArguments')
            ->willReturn(['$test']);

        $this->assertSame($expectedOutput, $representation->representation());
    }
}

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation;

/**
 * Class Param
 */
class Param extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Param $node */
        $node = $this->node;

        $byRef = $node->byRef ? '&' : '';
        $type = $node->type ? $node->type.' ' : '';
        $default = $node->default ? ' = '. $this->representate($node->default) : '';
        $variadic = $node->variadic ? '...' : '';

        return sprintf(
            '%s%s%s%s%s',
            $type,
            $variadic,
            $byRef,
            $this->representate($node->var),
            $default
        );
    }
}

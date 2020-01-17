<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Stmt;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Case_
 */
class Case_ extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = 'case %s:';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Stmt\Case_ $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->cond)
        );
    }
}

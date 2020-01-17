<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class New_
 */
class New_ extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = '(new %s(%s))';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\New_ $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->representate($node->class),
            $this->arguments($node->args)
        );
    }
}

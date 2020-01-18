<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Exit_
 */
class Exit_ extends AbstractRepresentation
{
    /** @var string */
    private const EXIT_STATEMENT = 'exit';

    /** @var string */
    private const FORMAT_REPRESENTATION = '%s(%s)';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Exit_ $node */
        $node = $this->node;

        if (null === $node->expr) {
            return self::EXIT_STATEMENT;
        }

        return sprintf(
            self::FORMAT_REPRESENTATION,
            self::EXIT_STATEMENT,
            $this->representate($node->expr)
        );
    }
}

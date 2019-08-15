<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Stmt;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Return_
 */
class Return_ extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Stmt\Return_ $node */
        $node = $this->node;

        return sprintf(
            'return %s',
            $this->representate($node->expr)
        );
    }
}

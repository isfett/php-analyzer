<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class Cast
 */
class Cast extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\Cast $node */
        $node = $this->node;

        $type = strtolower(substr(strrchr(get_class($node), '\\'), 1));
        if ('_' === substr($type, -1)) {
            $type = substr($type, 0, -1);
        }

        return sprintf(
            '(%s) %s',
            $type,
            $this->representate($node->expr)
        );
    }
}

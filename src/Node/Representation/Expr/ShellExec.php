<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ShellExec
 */
class ShellExec extends AbstractRepresentation
{
    /** @var string */
    private const FORMAT_REPRESENTATION = 'shell_exec(%s)';

    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ShellExec $node */
        $node = $this->node;

        return sprintf(
            self::FORMAT_REPRESENTATION,
            $this->arguments($node->parts)
        );
    }
}

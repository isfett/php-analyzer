<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\Representation\Expr;

use Isfett\PhpAnalyzer\Node\Representation\AbstractRepresentation;

/**
 * Class ShellExec
 */
class ShellExec extends AbstractRepresentation
{
    /**
     * @return string
     */
    public function representation(): string
    {
        /** @var \PhpParser\Node\Expr\ShellExec $node */
        $node = $this->node;

        return sprintf(
            'shell_exec(%s)',
            $this->arguments($node->parts)
        );
    }
}

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

use PhpParser\Node;

/**
 * Class NodeRepresentationClassDoesNotExistException
 */
class NodeRepresentationClassDoesNotExistException extends \LogicException
{
    /**
     * NodeRepresentationClassDoesNotExistException constructor.
     *
     * @param Node            $node
     * @param \Throwable|null $previous
     */
    public function __construct(Node $node, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'No Representation for node %s found',
            get_class($node)
        ), 0, $previous);
    }
}

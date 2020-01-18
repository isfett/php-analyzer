<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

use PhpParser\Node;

/**
 * Class NodeRepresentationClassDoesNotExistException
 */
class NodeRepresentationClassDoesNotExistException extends \LogicException
{
    /** @var int */
    private const ERROR_CODE = 0;

    /** @var string */
    private const ERROR_MESSAGE = 'No Representation for node %s found';

    /**
     * NodeRepresentationClassDoesNotExistException constructor.
     *
     * @param Node            $node
     * @param \Throwable|null $previous
     */
    public function __construct(Node $node, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                self::ERROR_MESSAGE,
                get_class($node)
            ),
            self::ERROR_CODE,
            $previous
        );
    }
}

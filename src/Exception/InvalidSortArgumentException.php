<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

use Doctrine\Common\Collections\Criteria;

/**
 * Class InvalidSortArgumentException
 */
class InvalidSortArgumentException extends \InvalidArgumentException
{
    /**
     * InvalidSortArgumentException constructor.
     *
     * @param string|null     $sort
     * @param \Throwable|null $previous
     */
    public function __construct(?string $sort, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(
            "Sort direction with '%s' is invalid, use '%s' or '%s' instead",
            strtolower($sort),
            strtolower(Criteria::ASC),
            strtolower(Criteria::DESC)
        ), 0, $previous);
    }
}

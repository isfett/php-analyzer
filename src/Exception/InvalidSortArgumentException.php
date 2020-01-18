<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

use Doctrine\Common\Collections\Criteria;

/**
 * Class InvalidSortArgumentException
 */
class InvalidSortArgumentException extends \InvalidArgumentException
{
    /** @var int */
    private const ERROR_CODE = 0;

    /** @var string */
    private const ERROR_MESSAGE = 'Sort direction with \'%s\' is invalid, use \'%s\' or \'%s\' instead';

    /**
     * InvalidSortArgumentException constructor.
     *
     * @param string|null     $sort
     * @param \Throwable|null $previous
     */
    public function __construct(?string $sort, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                self::ERROR_MESSAGE,
                strtolower($sort),
                strtolower(Criteria::ASC),
                strtolower(Criteria::DESC)
            ),
            self::ERROR_CODE,
            $previous
        );
    }
}

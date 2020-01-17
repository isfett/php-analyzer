<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

/**
 * Class InvalidSortConfigurationException
 */
class InvalidSortConfigurationException extends \RuntimeException
{
    /** @var int */
    private const ERROR_CODE = 0;

    /**
     * InvalidSortConfigurationException constructor.
     *
     * @param string          $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::ERROR_CODE, $previous);
    }
}

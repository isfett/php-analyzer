<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO\Configuration;

/**
 * Class SortField
 */
class SortField
{
    /** @var string */
    private $direction;

    /** @var string */
    private $field;

    /**
     * SortField constructor.
     *
     * @param string $field
     * @param string $direction
     */
    public function __construct(string $field, string $direction)
    {
        $this->direction = $direction;
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}

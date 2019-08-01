<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO;

/**
 * Class Condition
 */
class Condition
{
    /** @var string */
    private $condition;

    /** @var Occurrence */
    private $occurrence;

    /**
     * Condition constructor.
     *
     * @param string     $condition
     * @param Occurrence $occurrence
     */
    public function __construct(string $condition, Occurrence $occurrence)
    {
        $this->condition = $condition;
        $this->occurrence = $occurrence;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition(string $condition): void
    {
        $this->condition = $condition;
    }

    /**
     * @return Occurrence
     */
    public function getOccurrence(): Occurrence
    {
        return $this->occurrence;
    }
}

<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO;

/**
 * Class CountedCondition
 */
class CountedCondition
{
    /** @var string */
    private $condition;

    /** @var array<Occurrence> */
    private $occurrences = [];

    /**
     * CountedCondition constructor.
     *
     * @param string     $condition
     * @param Occurrence $occurrence
     */
    public function __construct(string $condition, Occurrence $occurrence)
    {
        $this->condition = $condition;
        $this->addOccurrence($occurrence);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->occurrences);
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return array
     */
    public function getOccurrences(): array
    {
        return $this->occurrences;
    }

    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function addOccurrence(Occurrence $occurrence): void
    {
        $this->occurrences[] = $occurrence;
    }
}

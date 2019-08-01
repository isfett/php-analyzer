<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO;

/**
 * Class CountedConditionList
 */
class CountedConditionList
{
    /** @var string */
    public const SORT_ASC = 'Asc';

    /** @var string */
    public const SORT_DESC = 'Desc';

    /** @var string */
    private $sortDirection;

    /**
     * @var array<CountedCondition>
     */
    private $countedConditions = [];

    /**
     * CountedConditionList constructor.
     *
     * @param string        $sortDirection
     */
    public function __construct(?string $sortDirection = null)
    {
        if (null === $sortDirection) {
            $sortDirection = self::SORT_ASC;
        }
        $this->sortDirection = $sortDirection;
    }

    /**
     * @return array<CountedCondition>
     */
    public function getCountedConditions(): array
    {
        return $this->countedConditions;
    }

    /**
     * @param int|null $maxEntries
     *
     * @return array<CountedCondition>
     */
    public function getCountedConditionsSorted(?int $maxEntries = null): array
    {
        $conditions = $this->countedConditions;

        if (null !== $maxEntries) {
            usort($conditions, [$this, 'sortDesc']); // max just work on desc
            $conditions = array_slice($conditions, 0, $maxEntries);

            if ($this->sortDirection === self::SORT_DESC) {
                return $conditions;
            }
        }

        $sortFunction = 'sort'.$this->sortDirection;
        usort($conditions, [$this, $sortFunction]);

        return $conditions;
    }

    /**
     * @param Condition $condition
     *
     * @return void
     */
    public function addCondition(Condition $condition): void
    {
        $foundKey = $this->checkAlreadyExistent($condition);

        if (null === $foundKey) {
            $hash = md5($condition->getCondition());
            $this->countedConditions[$hash] = new CountedCondition(
                $condition->getCondition(),
                $condition->getOccurrence()
            );
        } else {
            /** @var CountedCondition $countedCondition */
            $countedCondition = $this->countedConditions[$foundKey];
            $countedCondition->addOccurrence($condition->getOccurrence());
        }
    }

    /**
     * @param CountedCondition $a
     * @param CountedCondition $b
     *
     * @return int
     */
    private function sortAsc(CountedCondition $a, CountedCondition $b) {
        return $a->getCount() <=> $b->getCount();
    }

    /**
     * @param CountedCondition $a
     * @param CountedCondition $b
     *
     * @return int
     */
    private function sortDesc(CountedCondition $a, CountedCondition $b) {
        return $b->getCount() <=> $a->getCount();
    }

    /**
     * @param Condition $condition
     *
     * @return string|null
     */
    private function checkAlreadyExistent(Condition $condition): ?string
    {
        $hash = md5($condition->getCondition());

        if (array_key_exists($hash, $this->countedConditions)) {
            return $hash;
        }

        return null;
    }
}

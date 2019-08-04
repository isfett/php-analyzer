<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ConditionList
 */
class ConditionList
{
    /** @var ArrayCollection<Condition> */
    private $conditions;

    /**
     * ConditionList constructor.
     */
    public function __construct()
    {
        $this->conditions = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<Condition>
     */
    public function getConditions(): ArrayCollection
    {
        return $this->conditions;
    }

    /**
     * @param Condition $condition
     *
     * @return void
     */
    public function addCondition(Condition $condition): void
    {
        $this->conditions->add($condition);
    }
}

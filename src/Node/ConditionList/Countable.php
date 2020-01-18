<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Node\ConditionList;

use Doctrine\Common\Collections\ArrayCollection;
use Isfett\PhpAnalyzer\DAO\Condition;
use Isfett\PhpAnalyzer\DAO\CountedCondition;

/**
 * Class Countable
 */
class Countable
{
    /** @var ArrayCollection<CountedCondition> */
    private $countedConditions;

    /**
     * Countable constructor.
     */
    public function __construct()
    {
        $this->countedConditions = new ArrayCollection();
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
            $countedCondition = new CountedCondition(
                $condition->getCondition(),
                $condition->getOccurrence()
            );
            $this->countedConditions->set($hash, $countedCondition);
        } else {
            /** @var CountedCondition $countedCondition */
            $countedCondition = $this->countedConditions->get($foundKey);
            $countedCondition->addOccurrence($condition->getOccurrence());
        }
    }

    /**
     * @return ArrayCollection<CountedCondition>
     */
    public function getCountedConditions(): ArrayCollection
    {
        return $this->countedConditions;
    }

    /**
     * @param Condition $condition
     *
     * @return string|null
     */
    private function checkAlreadyExistent(Condition $condition): ?string
    {
        $hash = md5($condition->getCondition());

        if ($this->countedConditions->containsKey($hash)) {
            return $hash;
        }

        return null;
    }
}

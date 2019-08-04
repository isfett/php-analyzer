<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Isfett\PhpAnalyzer\DAO\Configuration\Sort;
use Isfett\PhpAnalyzer\DAO\Configuration\SortField;

/**
 * Class SortService
 */
class SortService
{
    /**
     * @param ArrayCollection $collection
     * @param Sort            $sortConfiguration
     *
     * @return ArrayCollection
     */
    public function sortArrayCollection(ArrayCollection $collection, Sort $sortConfiguration): ArrayCollection
    {
        $criteria = new Criteria(
            null,
            $this->getSortFields($sortConfiguration),
            $sortConfiguration->getFirstResult(),
            $sortConfiguration->getMaxResults()
        );

        return $collection->matching($criteria);
    }

    /**
     * @param Sort $sortConfiguration
     *
     * @return array
     */
    private function getSortFields(Sort $sortConfiguration): array
    {
        $sortFields = [];

        /** @var SortField $sortField */
        foreach ($sortConfiguration->getFields() as $sortField) {
            $sortFields[$sortField->getField()] = $sortField->getDirection();
        }

        return $sortFields;
    }
}

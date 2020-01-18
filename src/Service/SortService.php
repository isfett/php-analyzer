<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Isfett\PhpAnalyzer\DAO\Configuration\Sort;
use Isfett\PhpAnalyzer\DAO\Configuration\SortField;
use Isfett\PhpAnalyzer\Service\SortService\SortByNodeValues;

/**
 * Class SortService
 */
class SortService
{
    /** @var int */
    private const ARRAY_FIRST_KEY = 0;

    /** @var int */
    private const HUMAN_TO_COMPUTER_ARRAY_OFFSET = 1;

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
     * @param ArrayCollection $collection
     * @param Sort            $sortConfiguration
     *
     * @return ArrayCollection
     */
    public function sortOccurrenceCollectionByNodeValues(
        ArrayCollection $collection,
        Sort $sortConfiguration
    ): ArrayCollection {
        $collectionIterator = $collection->getIterator();
        $collectionIterator->uasort([new SortByNodeValues($sortConfiguration), SortByNodeValues::SORT_FUNCTION]);
        $collectionArray = iterator_to_array($collectionIterator, true);
        if (null !== $sortConfiguration->getFirstResult()) {
            $collectionArray = array_slice(
                $collectionArray,
                $sortConfiguration->getFirstResult() - self::HUMAN_TO_COMPUTER_ARRAY_OFFSET
            );
        }

        if (null !== $sortConfiguration->getMaxResults()) {
            $collectionArray = array_slice(
                $collectionArray,
                self::ARRAY_FIRST_KEY,
                $sortConfiguration->getMaxResults()
            );
        }

        return new ArrayCollection($collectionArray);
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

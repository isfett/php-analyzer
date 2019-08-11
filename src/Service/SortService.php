<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Isfett\PhpAnalyzer\DAO\Configuration\Sort;
use Isfett\PhpAnalyzer\DAO\Configuration\SortField;
use Isfett\PhpAnalyzer\DAO\Occurrence;
use PhpParser\Node;

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
        $collectionIterator->uasort(static function ($occurrenceA, $occurrenceB) use ($sortConfiguration) {
            /** @var Occurrence $occurrenceA */
            /** @var Occurrence $occurrenceB */
            /** @var Node\Scalar\LNumber|Node\Scalar\DNumber $nodeA */
            /** @var Node\Scalar\LNumber|Node\Scalar\DNumber $nodeB */
            $nodeA = $occurrenceA->getNode();
            $nodeB = $occurrenceB->getNode();

            $result = 0;

            /** @var SortField $sortField */
            foreach ($sortConfiguration->getFields() as $sortField) {
                $field = $sortField->getField();
                $result = $nodeA->$field <=> $nodeB->$field;
                if ('DESC' === $sortField->getDirection()) {
                    $result *= -1;
                }
                if (0 !== $result) {
                    return $result;
                }
            }

            return $result;
        });
        $collectionArray = iterator_to_array($collectionIterator, true);
        if (null !== $sortConfiguration->getFirstResult()) {
            $collectionArray = array_slice($collectionArray, $sortConfiguration->getFirstResult() - 1);
        }
        if (null !== $sortConfiguration->getMaxResults()) {
            $collectionArray = array_slice($collectionArray, 0, $sortConfiguration->getMaxResults());
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

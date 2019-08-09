<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Isfett\PhpAnalyzer\DAO\Configuration\Sort;
use Isfett\PhpAnalyzer\DAO\Configuration\SortField;
use Isfett\PhpAnalyzer\Exception\InvalidSortArgumentException;
use Isfett\PhpAnalyzer\Exception\InvalidSortConfigurationException;

/**
 * Class SortConfigurationBuilder
 */
class SortConfigurationBuilder implements SortConfigurationBuilderInterface
{
    /** @var int|null */
    private $firstResult;

    /** @var int|null */
    private $maxResults;

    /** @var ArrayCollection */
    private $sortFields;

    /**
     * SortConfigurationBuilder constructor.
     */
    public function __construct()
    {
        $this->sortFields = new ArrayCollection();
    }

    /**
     * @return Sort
     * @throws InvalidSortConfigurationException
     */
    public function getSortConfiguration(): Sort
    {
        if (0 === $this->sortFields->count()) {
            throw new InvalidSortConfigurationException('You need to add at least one sort field');
        }

        return new Sort($this->sortFields, $this->firstResult, $this->maxResults);
    }

    /**
     * @param int|null $maxResults
     *
     * @return SortConfigurationBuilderInterface
     */
    public function setMaxResults(?int $maxResults = null): SortConfigurationBuilderInterface
    {
        $this->maxResults = $maxResults;

        return $this;
    }

    /**
     * @param int|null $firstResult
     *
     * @return SortConfigurationBuilderInterface
     */
    public function setFirstResult(?int $firstResult = null): SortConfigurationBuilderInterface
    {
        $this->firstResult = $firstResult;

        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return SortConfigurationBuilderInterface
     * @throws InvalidSortArgumentException
     */
    public function addSortField(string $field, string $direction): SortConfigurationBuilderInterface
    {
        $direction = strtoupper($direction);
        if (Criteria::ASC !== $direction && Criteria::DESC !== $direction) {
            throw new InvalidSortArgumentException($direction);
        }

        $sortField = new SortField($field, $direction);
        $this->sortFields->add($sortField);

        return $this;
    }
}

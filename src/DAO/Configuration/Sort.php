<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO\Configuration;

use Doctrine\Common\Collections\Collection;

/**
 * Class Sort
 */
class Sort
{
    /** @var Collection<SortField> */
    private $fields;

    /** @var int|null */
    private $firstResult;

    /** @var int|null */
    private $maxResults;

    /**
     * Sort constructor.
     *
     * @param Collection $fields
     * @param int|null   $firstResult
     * @param int|null   $maxResults
     */
    public function __construct(Collection $fields, ?int $firstResult = null, ?int $maxResults = null)
    {
        $this->fields = $fields;
        $this->firstResult = $firstResult;
        $this->maxResults = $maxResults;
    }

    /**
     * @return Collection
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * @return int|null
     */
    public function getFirstResult(): ?int
    {
        return $this->firstResult;
    }

    /**
     * @return int|null
     */
    public function getMaxResults(): ?int
    {
        return $this->maxResults;
    }
}

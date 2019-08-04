<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Isfett\PhpAnalyzer\DAO\Configuration\Sort;

/**
 * Interface SortConfigurationBuilderInterface
 */
interface SortConfigurationBuilderInterface
{
    /**
     * @return Sort
     */
    public function getSortConfiguration(): Sort;

    /**
     * @param int|null $maxResults
     *
     * @return self
     */
    public function setMaxResults(?int $maxResults = null): self;

    /**
     * @param int|null $firstResult
     *
     * @return self
     */
    public function setFirstResult(?int $firstResult = null): self;

    /**
     * @param string $field
     * @param string $direction
     *
     * @return self
     */
    public function addSortField(string $field, string $direction): self;
}

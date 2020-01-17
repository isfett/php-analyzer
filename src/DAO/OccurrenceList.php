<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\DAO;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class OccurrenceList
 */
class OccurrenceList
{
    /** @var ArrayCollection<Occurrence> */
    private $occurrences;

    /**
     * OccurrenceList constructor.
     */
    public function __construct()
    {
        $this->occurrences = new ArrayCollection();
    }

    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function addOccurrence(Occurrence $occurrence): void
    {
        $this->occurrences->add($occurrence);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->occurrences->count();
    }

    /**
     * @return ArrayCollection
     */
    public function getOccurrences(): ArrayCollection
    {
        return $this->occurrences;
    }

    /**
     * @param Occurrence $occurrence
     *
     * @return void
     */
    public function removeOccurrence(Occurrence $occurrence): void
    {
        $this->occurrences->removeElement($occurrence);
    }
}

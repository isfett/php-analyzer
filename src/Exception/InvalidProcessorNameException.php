<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Exception;

use Isfett\PhpAnalyzer\Finder\Finder;

/**
 * Class InvalidProcessorNameException
 */
class InvalidProcessorNameException extends \LogicException
{
    /**
     * InvalidProcessorNameException constructor.
     *
     * @param string          $processorName
     * @param \Throwable|null $previous
     */
    public function __construct(string $processorName, ?\Throwable $previous = null)
    {
        $possibleProcessorNames = $this->getPossibleProcessorNames();
        parent::__construct(sprintf(
            'Processor with name %s does not exist. Possible processor-names are: %s',
            $processorName,
            implode(', ', $possibleProcessorNames)
        ), 0, $previous);
    }

    /**
     * @return array
     */
    private function getPossibleProcessorNames(): array
    {
        $finder = new Finder([dirname(__DIR__) . '/Node/Processor'], [], [], [], ['ProcessorInterface.php'], []);
        $finder->sortByName();
        $possibleVisitorNames = [];
        foreach ($finder->files() as $file) {
            $possibleVisitorNames[] = str_replace('Processor.php', '', $file->getRelativePathname());
        }

        return $possibleVisitorNames;
    }
}

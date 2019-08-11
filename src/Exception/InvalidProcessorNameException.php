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
     * @param string          $prefix
     * @param \Throwable|null $previous
     */
    public function __construct(string $processorName, string $prefix, ?\Throwable $previous = null)
    {
        $possibleProcessorNames = $this->getPossibleProcessorNames($prefix);
        parent::__construct(sprintf(
            'Processor with name %s does not exist. Possible Processors are: %s',
            $processorName,
            implode(', ', $possibleProcessorNames)
        ), 0, $previous);
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    private function getPossibleProcessorNames(string $prefix): array
    {
        $finder = new Finder([dirname(__DIR__) . '/Node/Processor/'.$prefix], [], [], [], ['ProcessorInterface.php'], []);
        $finder->sortByName();
        $possibleVisitorNames = [];
        foreach ($finder->getIterator() as $file) {
            $possibleVisitorNames[] = str_replace('Processor.php', '', $file->getRelativePathname());
        }

        return $possibleVisitorNames;
    }
}

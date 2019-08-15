<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Isfett\PhpAnalyzer\Exception\InvalidProcessorNameException;
use Isfett\PhpAnalyzer\Node\Processor\ProcessorInterface;

/**
 * Class ProcessorBuilder
 */
class ProcessorBuilder implements ProcessorBuilderInterface
{
    /** @var array */
    private $names = [];

    /** @var string */
    private $prefix = '';

    /**
     * @return ArrayCollection<ProcessorInterface>
     */
    public function getProcessors(): ArrayCollection
    {
        $visitors = new ArrayCollection();

        foreach ($this->names as $name) {
            $classname = sprintf('Isfett\\PhpAnalyzer\\Node\\Processor\\%s\\%sProcessor', $this->prefix, $name);

            if (!class_exists($classname)) {
                throw new InvalidProcessorNameException($name, $this->prefix);
            }

            $visitors->add(new $classname());
        }

        return $visitors;
    }

    /**
     * @param string $names
     *
     * @return ProcessorBuilderInterface
     */
    public function setNames(string $names): ProcessorBuilderInterface
    {
        if ('' === $names) {
            $this->names = [];
        } else {
            $this->names = explode(',', str_replace(', ', ',', $names));
        }

        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return VisitorBuilderInterface
     */
    public function setPrefix(string $prefix): ProcessorBuilderInterface
    {
        $this->prefix = $prefix;

        return $this;
    }
}

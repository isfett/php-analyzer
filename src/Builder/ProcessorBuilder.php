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
    /** @var string */
    private const COMMA = ',';

    /** @var string */
    private const COMMA_WITH_SPACE = ', ';

    /** @var string */
    private const EMPTY_STRING = '';

    /** @var string */
    private const FORMAT_NAMESPACE = 'Isfett\\PhpAnalyzer\\Node\\Processor\\%s\\%sProcessor';

    /** @var array */
    private $names = [];

    /** @var string */
    private $prefix = self::EMPTY_STRING;

    /**
     * @return ArrayCollection<ProcessorInterface>
     */
    public function getProcessors(): ArrayCollection
    {
        $visitors = new ArrayCollection();

        foreach ($this->names as $name) {
            $classname = sprintf(self::FORMAT_NAMESPACE, $this->prefix, $name);

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
        if (self::EMPTY_STRING === $names) {
            $this->names = [];
        } else {
            $this->names = explode(self::COMMA, str_replace(self::COMMA_WITH_SPACE, self::COMMA, $names));
        }

        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return ProcessorBuilderInterface
     */
    public function setPrefix(string $prefix): ProcessorBuilderInterface
    {
        $this->prefix = $prefix;

        return $this;
    }
}

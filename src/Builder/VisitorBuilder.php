<?php
declare(strict_types = 1);

namespace Isfett\PhpAnalyzer\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Isfett\PhpAnalyzer\Exception\InvalidVisitorNameException;
use Isfett\PhpAnalyzer\Node\Visitor\VisitorInterface;

/**
 * Class VisitorBuilder
 */
class VisitorBuilder implements VisitorBuilderInterface
{
    /** @var array */
    private $names = [];

    /** @var string */
    private $prefix = '';

    /**
     * @return ArrayCollection<VisitorInterface>
     */
    public function getVisitors(): ArrayCollection
    {
        $visitors = new ArrayCollection();

        foreach ($this->names as $name) {
            $classname = sprintf('Isfett\\PhpAnalyzer\\Node\\Visitor\\%s\\%sVisitor', $this->prefix, $name);

            if (!class_exists($classname)) {
                throw new InvalidVisitorNameException($name, $this->prefix);
            }

            $visitors->add(new $classname());
        }

        return $visitors;
    }

    /**
     * @param string $names
     *
     * @return VisitorBuilderInterface
     */
    public function setNames(string $names): VisitorBuilderInterface
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
    public function setPrefix(string $prefix): VisitorBuilderInterface
    {
        $this->prefix = $prefix;

        return $this;
    }
}

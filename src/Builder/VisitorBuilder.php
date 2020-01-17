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
    /** @var string */
    private const COMMA = ',';

    /** @var string */
    private const COMMA_WITH_SPACE = ', ';

    /** @var string */
    private const EMPTY_STRING = '';

    /** @var string */
    private const FORMAT_NAMESPACE = 'Isfett\\PhpAnalyzer\\Node\\Visitor\\%s\\%sVisitor';

    /** @var array */
    private $names = [];

    /** @var string */
    private $prefix = self::EMPTY_STRING;

    /**
     * @return ArrayCollection<VisitorInterface>
     */
    public function getVisitors(): ArrayCollection
    {
        $visitors = new ArrayCollection();

        foreach ($this->names as $name) {
            $classname = sprintf(self::FORMAT_NAMESPACE, $this->prefix, $name);

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
     * @return VisitorBuilderInterface
     */
    public function setPrefix(string $prefix): VisitorBuilderInterface
    {
        $this->prefix = $prefix;

        return $this;
    }
}

<?php
declare(strict_types = 1);

/**
 * Class Foo
 */
class Foo
{
    /** @var int */
    private const TEST = 4;

    /** @var int */
    private $test = self::TEST;

    /** @var int */
    private $foo = 0;

    /** @var string */
    private $foobar = 'bar';
 
    /**
     * @return int
     */
    public function getTest(): int
    {
        return $this->test;
    }

    /**
     * @return int
     */
    public function getFoo(): int
    {
        return $this->foo;
    }

    public function foobar()
    {
        $foo = [
            15 => $this->foobar,
        ];
        return $foo;
    }
}

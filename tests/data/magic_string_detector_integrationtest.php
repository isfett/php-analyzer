<?php

define('test', 'prod');

$variable = 'foo';

class MagicStringTestClass
{
    public const MAGICSTRING = 'foo';

    private $variable = 'bar';

    public function test($input = 'php') {
        if ($input === 'php') {
            return 'php';
        }

        switch($input) {
            case 'java':
                return '';
        }

        $array = [
            'name' => 'Name',
            'age' => 13,
            'adult' => $input > 18,
            18,
            123 => 1234,
            '1234' => 1234,
        ];

        if ($input !== 'c++');
        if (PHP_EXTRA_VERSION !== '-13ubuntu3.2');

        $value = $input . 'additional';
    }

    private const MAGICSTRING2 = 'de' . '_' . 'DE';

    public function returnString() {
        return 'string';
    }

    public function functionCalls()
    {
        return [
            intval(100),
            floatval(3.14),
            strval('10')
        ];
    }
}

<?php

class MagicStringTestClass
{
    public const MAGICSTRING = 'foo';

    private $variable = 'bar';

    public function test($input = 'bar')
    {
        if ($input === 'bar') {
            return 'barx';
        }

        if ('' === $input) {
            return  [
                'test' => '12',
            ];
        }

        switch ($input) {
            case '8':
                break;
        }

        $assignment = 'foo';

        $operation = 'foo' . 'bar';

        $testSubstring = substr('testString', 0, 4);

        return 'bar';
    }
}

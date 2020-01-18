<?php

class MagicNumberTestClass
{
    public const MAGICNUMBER = 3;

    private $variable = 6;

    public function test($input = 4)
    {
        if ($input > 2) {
            return 15;
        }

        if (0 === $input) {
            return  [
                'test' => 12,
            ];
        }

        switch ($input) {
            case 8:
                break;
        }

        $assignment = 26;

        $rounded = round($this->variable, 17);

        $ternary = $assignment ? 2 : 3;

        return 5;
    }
}

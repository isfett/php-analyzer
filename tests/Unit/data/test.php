<?php
declare(strict_types = 1);

$user = null; // not logged in
if ($user && isset($_SESSION['user'], $_SESSION['user']['id']) && 2019 === date('Y')) {
    echo 'wow';
}

if(   date('Y') === 2019) {
    echo '2019!!!';
} elseif ($user) {
    echo 'x)';
}

echo date('Y') === 2019 ? 'x' :'y';

if ("\\$x") {}

function abc($x, $y): bool
{
    return $x ===
        $y;
}

if (\DIRECTORY_SEPARATOR === '\\') {
    echo 'y';
}

if (!(1 === 2 && strtolower('A') === 'a')) {
    echo 'y';
}

if ('a' !== \strtolower('A')) {
    echo 'y';
}

//if((fn() => 1)() === 1) { echo 'x'; } else { echo 'y'; }

if (static function& (string $test) use ($user) { return true; }) {}

if ((fn() => 1)()) {}

if($d = (static fn&($number): int => $number * $number)(5) === 25) { echo 'x'; } else { echo 'y'; }

if (['Cowabunga' => &$item] === 3 || [1 => &$item] === 3 || [...$items]|| [...$items]) {
    echo 'x';
}

if (($map[] = abc(1, 2, 3)) === null) {}

if (print('x')) {}

if(exit) {}

if($d = clone $x) {}

if($a = eval('return 1337;')) {}
<?php

$x = 'c';
if ('a' === $x || ('b' === $x && !empty($x))) {
    // do smth
}

if (!('a' === $x || ('b' === $x && 'c' !== $x))) {
    // do smth
}
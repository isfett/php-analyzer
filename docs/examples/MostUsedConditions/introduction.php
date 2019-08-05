<?php

$user = null;

if ($user) {
    echo sprintf('Welcome %s', $user->getUsername());
}

if(   date('Y') === 2019) {
    echo '2019!!!';
} elseif ($user) {
    echo 'x)';
}

echo date('Y') === 2019 ? 'x' :'y';

$page = $_GET['page'] ?? 1;

<?php

if(date('Y') === 2019) {
    echo '2019!!!';
} elseif (date('Y') === 2018) {
    echo '2018!!!';
} elseif (date('Y') === 2017) {
    echo '2017!!!';
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$page = $_GET['page'] ?? 1;

function isAnonymous(?User $user): bool
{
    return null === $user;
}

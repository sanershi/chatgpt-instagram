<?php 
function generateRandomUsername($conn) {
    $userPrefix = "user";
    $randomNumbers = mt_rand(100000000000000, 999999999999999);
    $username = $userPrefix . $randomNumbers;
    return $username;
}

?>
<?php
require_once 'auth.php';

if(isset(Authorization::$current))
    Authorization::$current->logoff();

header('Location: /');
?>


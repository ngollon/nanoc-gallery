<?php
require_once '../auth.php';

if(isset($_GET['q'])) {
    $q = $_GET['q'];
} else {
    header('Location: /index.html'); 
}

if(isset(Authorization::$current) && Authorization::$current->isLoggedIn) {
    $mime = content_type($q);
    header('X-Accel-Redirect: /internal/' . $q);
    header('Content-Type: ' . $mime);
    if($mime == 'image/jpeg') {
        header("Expires: 31 December 2037 23:59:59 GMT");
        header('Cache-Control: max-age=315360000');
    }
} else {
    header('Location: /');     
}

function content_type($filename)
{
    $ext = strtolower(array_pop(explode('.',$filename)));
    if($ext == 'jpg')
        return 'image/jpeg';
    else
        return 'text/html';
}

?>
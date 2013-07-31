<?php
session_start();

if(isset($_GET['q']))
    $q = $_GET['q'];
else
    header('Location: /index.html'); 

if(!isset($_SESSION['email']) || !isset($_SESSION['password']) || !try_login($_SESSION['email'], $_SESSION['password']))
{
    header('Location: /');
}
else
{
    $mime = content_type($q);
    header('X-Accel-Redirect: /internal/' . $q);
    header('Content-Type: ' . $mime);
}

function content_type($filename)
{
    $ext = strtolower(array_pop(explode('.',$filename)));
    if($ext == 'jpg')
        return 'image/jpeg';
    else
        return 'text/html';
}

function try_login($email, $password)
{
    $id = sha1(strtolower($email));
    $file = '../../users/' . $id;
    if(!file_exists($file))
        return false;

    $data = json_decode(file_get_contents($file));
    if(!$data->active)
        return false;

    if(crypt($password, $data->password) != $data->password)
        return false;
    return true;
}

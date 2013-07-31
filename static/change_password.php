<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['email']) || !isset($_SESSION['password']) || !try_login($_SESSION['email'], $_SESSION['password']))
    failure('Du bist momentan nicht eingeloggt.');

if(!isset($_POST['old_password']) || !isset($_POST['new_password']))
    failure('Das Login Script sollte durch das korrekte Formular aufgerufen werden.');

$email = $_SESSION['email'];
$password = $_SESSION['password'];
$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];

if($password != $old_password)
    failure("Das alte Passwort war nicht korrekt.");

$id = sha1(strtolower($email));
$file = '../users/' . $id;
if(!file_exists($file))
    failure('Dieser Benutzer ist nicht registriert. Das sollte nicht passieren!');

$data = json_decode(file_get_contents($file));
$data->password = crypt($new_password);    
file_put_contents($file, json_encode($data));
    
$_SESSION['password'] = $new_password;

success('Passwort geändert.');

function success($message){
    $result = array();
    $result['success'] = true;
    $result['message'] = $message;    
    exit(json_encode($result));
}

function failure($message){
    $result = array();
    $result['success'] = false;
    $result['message'] = $message;    
    exit(json_encode($result));
}


function try_login($email, $password)
{
    $id = sha1(strtolower($email));
    $file = '../users/' . $id;
    if(!file_exists($file))
        return false;

    $data = json_decode(file_get_contents($file));
    if(!$data->active)
        return false;

    if(crypt($password, $data->password) != $data->password)
        return false;
    return true;
}


?>

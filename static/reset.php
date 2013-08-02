<?php
session_start();
header('Content-Type: application/json');

if(!isset($_POST['email']))
    failure('Das Login Script sollte durch das korrekte Formular aufgerufen werden.');

$email = $_POST['email'];

$id = sha1(strtolower($email));
$file = '../users/' . $id;
if(!file_exists($file))
    failure('Dieser Benutzer ist nicht registriert.');

$new_password = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);

$message = "Hallo\n Dein neues Passwort ist " . $new_password;
$header = "From: webmaster@ameskamp.de";
mail($email, 'Neues Passwort', $message, $header);

$data = json_decode(file_get_contents($file));
$data->reset_password = crypt($new_password);    
file_put_contents($file, json_encode($data));

success('Ein neues Passwort wurde an Deine E-Mail Adresse geschickt.');

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

?>

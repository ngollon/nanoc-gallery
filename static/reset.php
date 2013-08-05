<?php
require_once 'auth.php';
require_once 'json_helpers.php';


if(!isset($_POST['email']))
    failure('Das Login Script sollte durch das korrekte Formular aufgerufen werden.');

$email = $_POST['email'];

$auth = NEW Authorization($email);

if(!$auth->exists)
    failure('Dieser Benutzer ist nicht registriert.');

$newPassword = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);

$message = "Hallo\n Dein neues Passwort ist " . $newPassword;
$header = "From: webmaster@ameskamp.de";
mail($email, 'Neues Passwort', $message, $header);

$auth->setPassword($newPassword);

success('Ein neues Passwort wurde an Deine E-Mail Adresse geschickt.');

?>

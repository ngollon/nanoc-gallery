<?php
require_once 'auth.php';
require_once 'json_helpers.php';

if(!isset($_POST['email']) || !isset($_POST['password']))
    failure('Das Login Script sollte durch das korrekte Formular aufgerufen werden.');

$email = $_POST['email'];
$password = $_POST['password'];

$auth = NEW Authorization(strtolower($email));

if(!$auth->exists)
    failure('Dieser Benutzer ist noch nicht registriert.');

if(!$auth->active)
    failure('Dieser Benutzer ist noch nicht freigeschaltet. Wenn Du denkst, wir haben Dich vergessen, ruf einfach an.');

$auth->tryLoginWithPassword($password);

if(!$auth->loggedIn)   
    failure('Das Passwort ist nicht korrekt.');

if(isset($_POST['remember']))
    $auth->persist();

success('Dieser Text sollte nicht angezeigt wergen. Bitte aktiviere JavaScript.');

?>

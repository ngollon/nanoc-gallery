<?php

require_once 'auth.php';
require_once 'json_helpers.php';

$auth = Authorization::$current;

if(!isset($auth) || !$auth->loggedIn)
    failure('Du bist momentan nicht eingeloggt.');

if(!isset($_POST['old_password']) || !isset($_POST['new_password']))
    failure('Das Login Script sollte durch das korrekte Formular aufgerufen werden.');

$oldPassword = $_POST['old_password'];
$newPassword = $_POST['new_password'];

if(!$auth->tryLoginWithPassword($oldPassword))
    failure("Das alte Passwort war nicht korrekt.");

$auth->setPassword($newPassword);
    
if(!$auth->tryLoginWithPassword($newPassword))
    failure('Ein Fehler ist aufgetreten beim Ändern des Passwortes. Das sollte nicht passieren.');

success('Passwort geändert.');

?>

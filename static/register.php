<?php
require_once 'auth.php';
require_once 'json_helpers.php';

if(!isset($_POST['email']) || !isset($_POST['password']))
    failure('Das Login Script sollte durch das korrekte Formular aufgerufen werden.');

$email = $_POST['email'];
$password = $_POST['password'];

if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    failure('Das ist keine gültige E-Mail Adresse.');

$auth = NEW Authorization($email);

if($auth->exists)
{
    if(!$auth->active)
        failure('Diese E-Mail Adresse ist schon registriert, aber noch nicht freigeschaltet.');
    failure('Diese E-Mail Adresse ist schon registriert und freigeschaltet. Melde Dich doch einfach an!');
}

$auth->setPassword($password);

$message = "Ein neuer Benutzer hat sich angemeldet.\r\nE-Mail: " . $email . "\r\nId: " . $id;
$header = "From: webmaster@ameskamp.de";
mail("jens@ameskamp.de", 'Neuer Benutzer', $message, $header);

success('Registrierung erfolgreich. Wir werden Dich so bald wie möglich freischalten und Dir dann eine E-Mail schicken.');

?>
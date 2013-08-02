<?php
$result = array();

header('Content-Type: application/json');

$result = array();

if(!isset($_POST['email']) || !isset($_POST['password']))
    failure('Das Login Script sollte durch das korrekte Formular aufgerufen werden.');

$email = $_POST['email'];
$password = $_POST['password'];

if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    failure('Das ist keine gültige E-Mail Adresse.');

$id = sha1(strtolower($email));
$file = '../users/' . $id;

if(file_exists($file))
{
    $user = json_decode(file_get_contents($file));
    if(!$user->active)
        failure('Diese E-Mail Adresse ist schon registriert, aber noch nicht freigeschaltet.');
    failure('Diese E-Mail Adresse ist schon registriert und freigeschaltet. Melde Dich doch einfach an!');
}

$data = array();
$data['active'] = false;
$data['email'] = $email;
$data['password'] = crypt($password);
file_put_contents($file, json_encode($data));


$message = "Ein neuer Benutzer hat sich angemeldet.\r\nE-Mail: " . $email . "\r\nId: " . $id;
$header = "From: webmaster@ameskamp.de";
mail("jens@ameskamp.de", 'Neuer Benutzer', $message, $header);


success('Registrierung erfolgreich. Wir werden Dich so bald wie möglich freischalten und Dir dann eine E-Mail schicken.');


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


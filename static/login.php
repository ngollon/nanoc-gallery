<?php
session_start();
header('Content-Type: application/json');

$result = array();

if(!isset($_POST['email']) || !isset($_POST['password']))
    failure('Das Login Script sollte durch das korrekte Formular aufgerufen werden.');

$email = $_POST['email'];
$password = $_POST['password'];

$id = sha1(strtolower($email));
$file = '../users/' . $id;
if(!file_exists($file))
    failure('Dieser Benutzer ist noch nicht registriert.');

$data = json_decode(file_get_contents($file));
if(!$data->active)
    failure('Dieser Benutzer ist noch nicht freigeschaltet. Wenn Du denkst, wir haben Dich vergessen, ruf einfach an.');


if(crypt($password, $data->password) != $data->password)
{
    if(isset($data->reset_password) && crypt($password, $data->reset_password) == $data->reset_password) {
        $data->password = $data->reset_password;
        unset($data->reset_password);
        file_put_contents($file, json_encode($data));
    } else {
        $zp_hash_seed = "HYA/uCi<x45F!z~jy%I6mn13]d-8vu";
        $zp_hash = sha1($data->email.$password.$zp_hash_seed);
        if($data->password == $zp_hash) { // Old Zenphoto User
            $data->password = crypt($password);
            file_put_contents($file, json_encode($data));
        }
        else{
           failure('Das Passwort ist nicht korrekt.');
        }
    }
}
    
$_SESSION['email'] = $email;
$_SESSION['password'] = $password;

success('Dieser Text sollte nicht angezeigt wergen. Bitte aktiviere JavaScript.');

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

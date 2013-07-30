<?php
  session_start();
  header('Content-Type: application/json');
  
  $result = array();
  
  if(!isset($_POST['email']) || !isset($_POST['password']))
  {
    $result['success'] = false;
    $result['message'] = 'Das Login Script sollte durch das korrekte Formular aufgerufen werden.';
    echo json_encode($result);
    return;
  }

  $email = $_POST['email'];
  $password = $_POST['password'];
  
  if(try_login($email, $password))
  {
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;
    
    $result['success'] = true;
    $result['message'] = 'Wenn Du dies siehst, hast Du Javascript nicht aktiviert.';    
  }
  else
  {
    $result['success'] = false;
    $result['message'] = 'E-Mail Adresse oder Passwort sind unbekannt.';
    session_destroy();
  }
    
  echo json_encode($result);
  
  function try_login($user, $password)
  {
    if($user == 'demo' && $password == 'demo')
    {
      return true;
    }  
    return false;
  }  
?>

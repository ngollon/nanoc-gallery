<?php
  session_start();
  
  $result = array();
  $result['target'] = '/gallery';  
  
  if(try_login($email, $password)
  {
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;
    
    $result['result'] = 'success';    
  }
  else
  {
    $result['result'] = 'E-Mail Adresse oder Passwort sind unbekannt.'
    session_destory();
  }
  
  
  return json_encode($result);
  
  function try_login($user, $password)
  {
    if($user == 'demo' && $password == 'demo')
    {
      return true;
    }  
    return false;
  }  
?>

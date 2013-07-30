<?php
  session_start();

  if(isset($_GET['q']))
  {
    $q = $_GET['q'];
  }
  else
  {
    header('Location: /index.html'); 
  }

  $result = array();
 
  if(   !isset($_SESSION['email'])
     || !isset($_SESSION['password'])
     || !try_login($_SESSION['email'], $_SESSION['password']))
  {
    header('Location: /index.html?target=' . $q);
  }
  else
  {
    $mime = content_type($q);
    header('X-Accel-Redirect: /internal/' . $q);
    header('Content-Type: ' . $mime);
  }

  function content_type($filename)
  {
    $ext = strtolower(array_pop(explode('.',$filename)));
    if($ext == 'jpg')
        return 'image/jpeg';
    else
        return 'text/html';
  }

  function try_login($user, $password)
  {
    if($user == 'demo' && $password == 'demo')
    {
      return true;
    }  
    return false;
  }  
?>

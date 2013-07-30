<?php
    $result = array();

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

    if(!try_create_user($email, $password)){
        $result['success'] = false;
        $result['message'] = "Diese E-Mail Adresse ist bereits registiert. Melde Dich doch an!";
    }else{
        $result['success'] = true;
        $result['message'] = "Registration erfolgreich. Wir werden Dich so bald wie möglich freischalten und Dir dann eine E-Mail schicken.";
    }
    
    echo json_encode($result);

    function try_create_user($email, $password){
        if($email == "demo")
            return false;
        else
            return true;
    }
?>

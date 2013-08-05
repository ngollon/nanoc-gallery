<?php
header('Content-Type: application/json');

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

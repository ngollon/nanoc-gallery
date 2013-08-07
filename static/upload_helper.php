<?php

require_once 'auth.php';
require_once 'json_helpers.php';

if(!isset(Authorization::$current) || !Authorization::$current->loggedIn) {
      exit(json_encode(array('error' => 'Uploads sind nur für angemeldete Benutzer möglich.')));
}

// Include the uploader class
require_once 'qqFileUploader.php';

$uploader = new qqFileUploader();

// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
$uploader->allowedExtensions = array('jpeg', 'jpg');

// Specify max file size in bytes.
$uploader->sizeLimit = 20 * 1024 * 1024;

// Specify the input name set in the javascript.
$uploader->inputName = 'qqfile';

// If you want to use resume feature for uploader, specify the folder to save parts.
$uploader->chunksFolder = '/tmp';


if(!isset($_POST['album']))
    exit(json_encode(array('error' => 'Kein Album angegeben.')));

$target_folder = '/srv/images/originals/' . $_POST['album'];

if(!is_dir($target_folder))
    exit(json_encode(array('error' => 'Das Albumverzeichnis '.$target_folder.' wurde nicht gefunden.')));

// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
$result = $uploader->handleUpload($target_folder);

// To return a name used for uploaded file you can use the following line.
$result['uploadName'] = $uploader->getUploadName();

touch('/srv/images/.updated');

echo json_encode($result);

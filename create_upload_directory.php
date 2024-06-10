<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function create_upload_directory()
{
    $uploadDirectory = 'path/to/my/upload/directory/';

    if (!file_exists($uploadDirectory)) {
        echo "Upload directory: " . realpath($uploadDirectory);

        if (!mkdir($uploadDirectory, 0777, true)) {
            die('Failed to create upload directory');
        }
    }
}

create_upload_directory();

?>


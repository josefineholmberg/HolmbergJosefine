<?php 
session_start(); 

$_SESSION = array();


if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 86400, '/');
    session_destroy();
}

header("Location: login.html");
exit();

?>
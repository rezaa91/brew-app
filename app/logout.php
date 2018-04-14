<?php
session_start();


if(isset($_SESSION['id'])){//if btn pressed when logged in, remove session and log user out
    #remove session
    session_destroy();
    $_SESSION=[];
    setcookie('PHPSESSID');
    
    header('location:index.php');
    
}else{//if accessed incorrectly
    header('location:index.php');
}
?>
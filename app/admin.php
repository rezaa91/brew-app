<?php

session_start();

if(isset($_SESSION['id']) && $_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT'])){ //if user logged in
    $page_title = "Admin Settings";
    $css = '../src/css/admin.css';
    include_once('../includes/header.html');
    include_once('../includes/navigation.html');
    include_once('../includes/db_connection.php');
    
    
    //form validation for changing password
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        
        //validate change password form
        if(isset($_POST['change']) && !empty($_POST['change'])){
            $change_pass = mysqli_real_escape_string($dbc, trim($_POST['change']));
        }else{
            $change_pass=null;
        }
        
        if(isset($_POST['confirm']) && !empty($_POST['confirm'])){
            $confirm_pass = mysqli_real_escape_string($dbc, trim($_POST['confirm']));
        }else{
            $confirm_pass=null;
        }
        
        
        
        
        //if both values are the same - then change password on database
        if($confirm_pass && $change_pass){
            
            if($confirm_pass == $change_pass){
                $q = "SELECT email, pass FROM admin WHERE email='{$_SESSION['email']}'";
                $r = mysqli_query($dbc, $q);

                if($r){
                    $q = "UPDATE admin SET pass=SHA1('$change_pass') WHERE email='{$_SESSION['email']}'";
                    $r = mysqli_query($dbc, $q);

                    if($r){
                        $pass_update = "Password changed successfully";
                    }else{
                        $pass_update = "We could not change your password at this time, please try again later";
                    }
                }else{
                    echo mysqli_error($dbc);
                }
            }else{
                $pass_update = "new password and password confirmation do not match" .mysqli_error($dbc);
            }
            
            
        }else{
            $pass_update = "Please fill in all fields" .mysqli_error($dbc);
        } 
    }//end of form handling IF
    
    
    
    
    $q = "SELECT email, pass FROM admin WHERE admin_id=$_SESSION[id]";
    $r = mysqli_query($dbc,$q);
    
    if($r){
        //display admin information
        while($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
            $_SESSION['email'] = $row['email'];

            echo "<div class='container'>
            <h3 id='admin-title'>Administrative Settings</h3>
            <div class='row' id='admin-content'>
                <div class='col-md-6' id='admin-form-wrapper-content'>
                    <form role='form' id='admin-form' action='admin.php' method='post'>
                        <div class='form-group'>
                            <label for='email'>EMAIL:</label><br />
                            <input type='email' class='form-control' name='email' value='".$row['email']."'/>
                        </div>
                        
                        <div class='form-group'>
                            <label for='change_pass'>CHANGE PASSWORD:</label><br />
                            <input type='password' class='form-control' name='change' />
                        </div>
                        
                        <div class='form-group'>
                            <label for='confirm_pass'>CONFIRM PASSWORD:</label><br />
                            <input type='password' class='form-control' name='confirm'/>
                        </div>
                        
                        <div class='form-group'>
                            <input type='submit' class='btn btn-default' id='confirm-btn' value='confirm'/>
                            <a type='button' class='btn btn-danger' href='delete_account.php'>DELETE ACCOUNT</a>
                        </div>
                    </form>";
            if(isset($pass_update)){
                echo "<div class='error_msg'><span>* $pass_update</span></div>";
            }
            echo "</div>";
            }
            echo"<div class='col-md-6' id='admin-wrapper-content'><h4 class='text-center'>Administrators <a href='add_admin.php' class='btn' id='add-admin-btn'>+</a></h4>";
            
        $q = "SELECT email FROM admin";
        $r = mysqli_query($dbc, $q);
        if($r){
            while($row = mysqli_fetch_array($r,MYSQLI_ASSOC)){
                echo "<ul>
                    <li>{$row['email']}</li>
                </ul>";       
            }
        }
        echo "</div></div></div";
    }
    
    mysqli_free_result($r);
    mysqli_close($dbc);
    
    
    
}else{//if accessed in error - redirect to homepage. destroy sessions/cookies for extra security
    session_destroy();
    setcookie('PHPSESSID');
    $_SESSION=[];
    
    header('location:index.php');
}

?>
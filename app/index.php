<?php

session_start();

//handle form from login
if($_SERVER['REQUEST_METHOD'] == "POST"){    
    #include database
    include_once('../includes/db_connection.php');
    
    //validate form
    $errors = [];
    
    #email
    if(isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
    }else{
        $email = null;
        $errors[] = "Please enter a valid email address";
    }
    
    #password
    if(isset($_POST['pass']) && !empty($_POST['pass'])){
        $pass = mysqli_real_escape_string($dbc, trim($_POST['pass']));
    }else{
        $pass = null;
        $errors[] = "Please enter your password";
    }
    
    
    //if form filled in correctly then show page
    if($email && $pass){
        //check if correct email and password in database
        $q = "SELECT admin_id, email, pass FROM admin WHERE email='$email' && pass=SHA1('$pass')";
        $r = mysqli_query($dbc, $q);
        
        //if query ran ok
        if(mysqli_num_rows($r) == 1){ //if in database
            unset($_SESSION['errors']);
            #store OS and broswer in session
            $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
            
            while($row = mysqli_fetch_array($r)){
                #store admin id and email in array
                $_SESSION['id'] = $row['admin_id']; 
                $_SESSION['email'] = $row['email'];


                #set page title and include header and navigation - incl page specific stylesheet
                $css = '../src/css/index.css';
                $page_title = "TEA APP";
                include_once('../includes/header.html');
                include_once('../includes/navigation.html');

                #body content
                echo '<div class="container-fluid jumbotron text-center" id="call-round">
                <a class="btn btn-lg btn-primary" href="call_round.php">CALL ROUND</a>
                </div>';

                #include footer
                include_once('../includes/footer.html');
            }
            

            //free result and close database
            mysqli_free_result($r);
            mysqli_close($dbc);
            
            
        }else{//incorrect email/password combination
            $errors[] = "Incorrect email/password combination";
            $_SESSION['errors'] = $errors;
            header('location:login.php');
        }
        
        
        
        
    }else{ //if form filled incorrectly then redirect page back to login.php
        $_SESSION['errors'] = $errors;
        header('location:login.php');
    }
    

   
    
    
    
}else{
    //relocate user to login page if page accessed in error
    header('location:login.php');
}





?>

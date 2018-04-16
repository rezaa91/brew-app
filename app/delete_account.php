<?php
session_start();

if(isset($_SESSION['id']) && $_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT'])){
    
    if($_SERVER['REQUEST_METHOD'] == "POST"){ //handle form
        include_once('../includes/db_connection.php');
        
        //validate form
        $errors=[];
        
        #email
        if(isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
        }else{
            $email = null;
            $errors[] = "Invalid email address entered";
        }
        
        #password
        if(isset($_POST['pass']) && !empty($_POST['pass'])){
            $pass = mysqli_real_escape_string($dbc, trim($_POST['pass']));
        }else{
            $pass = null;
            $errors[] = "Invalid password entered";
        }
        
        
        //if valid email and password entered - check if in database
        //only allow to delete current logged in user
        if($email && $pass){
            $q = "SELECT admin_id FROM admin WHERE email='$email' && pass=SHA1('$pass')"; //find user
            $r = mysqli_query($dbc, $q);
            
            if(mysqli_num_rows($r) == 1){//i.e. correct data
                $q = "DELETE FROM admin WHERE email='$email'";
                $r = mysqli_query($dbc, $q);
                
                if(mysqli_affected_rows($dbc) == 1){//if deleted successfully - delete session and redirect user to index
                    session_destroy();
                    $_SESSION=[];
                    setcookie('PHPSESSID');
                    header('location:index.php');
                }else{
                    echo "ERROR: please <a href='index.php'>try again</a>. ".mysqli_error($dbc);
                }
                
                
            }else{//incorrect data
                include_once('../includes/header.html');
                include_once('../includes/navigation.html');
                echo '<div class="container">
                    <p class="lead">Incorrect email and/or password. Please <a href="delete_account.php">try again</a>
                </div>';
            }
            
            
            
        }else{//if invalid email and/or password
            include_once('../includes/header.html');
            include_once('../includes/navigation.html');
            
            echo "<div class='container'><span>ERROR:</span><ul>";
            foreach($errors as $err){
                echo "<li>$err</li>";
            }
            echo "</ul><a href='delete_account.php'>TRY AGAIN</a></div>";
            
            include_once('../includes/footer.html');
        }
        
        
        
    }else{ //display form
        $page_title = "Delete Account";
        $css = '../src/css/delete_account.css';
        include('../includes/header.html');
        include('../includes/navigation.html');
        
        echo '<div class="container" id="form-wrapper">
            <h4>Are you sure you want to delete your account?</h4>
            <form role="form" action="delete_account.php" method="POST">
                <div class="form-group">
                    <label for="email">EMAIL: </label>
                    <input type="text" name="email" class="form-control" value="'.$_SESSION['email'].'" readonly />
                </div>
                
                <div class="form-group">
                    <label for="pass">CONFIRM PASSWORD: </label>
                    <input type="password" name="pass" class="form-control" />
                </div>
                
                <div class="form-group">
                    <input type="submit" name="submit" id="submit" class="btn btn-danger form-control" value="DELETE ACCOUNT" />
                </div>
                
            </form>
        </div>';
    }
    
    
    
}else{//if page accessed when not logged in - send back to home page
    session_destroy();
    $_SESSION=[];
    setcookie('PHPSESSID');
    header('location:index.php');    
}

?>
<?php

session_start();

if(isset($_SESSION['id']) && $_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT'])){ //page can only be accessed if logged in
    $page_title = "Add Administrator";
    $css = '../src/css/add_admin.css';
    include_once('../includes/header.html');
    include_once('../includes/navigation.html');
    include_once('../includes/db_connection.php');
    
    //handle form
    if($_SERVER['REQUEST_METHOD'] == "POST"){
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
            $errors[] = "Please enter a valid password";
        }
        
        //if valid email and password
        if($email && $pass){
            $q = "SELECT email FROM admin WHERE email='$email'"; //check no current email exists in DB
            $r = mysqli_query($dbc, $q);
            //unique email address
            if(mysqli_num_rows($r) == 0){ 
                $q = "INSERT INTO admin (email, pass) VALUES('$email', SHA1('$pass'))";
                $r = mysqli_query($dbc,$q);
                
                if(mysqli_affected_rows($dbc) == 1){//database updated if 1 row has been affected
                    $outcome = "Administrator has been added. Please <a href='index.php'>Login</a>";
                }else{ //if affected rows does not equal 1 then an error has occured
                    $outcome = "Sorry, something went wrong. Please <a href='add_admin.php'>try again</a>. ".mysqli_error($dbc);
                }
                
            }else{ //email already exists if query returns more than 0 results
                $outcome = "Email already exists. Please <a href='add_admin.php'>try again</a>";
            }
            
            echo "<div class='container outcome'>$outcome</div>";
            
        }else{
            echo "<div class='container outcome'><span>Errors:</span><ul>";
            foreach($errors as $err){
                echo "<li>$err</li>";
            }
            echo "</ul><a href='add_admin.php'>Try again</a></div>";
            $_POST=[];
        }//end of form handling
        

    }else{//if not shown yet then show form
        echo '<div class="container" id="form-wrapper">
            <div class="row">
                <div class="col-md-6" id="img-wrapper">
                    <img class="img-fluid" src="../src/img/coffee-1.jpg" />
                </div>
                
                <div class="col-md-6">
                    <h4>Add New Administrator Information</h4>
                    <form role="form" action="add_admin.php" method="post">
                        <div class="form-group">
                            <label for="email">EMAIL</label><br />
                            <input type="text" name="email" class="form-control"  />
                        </div>

                        <div class="form-group">
                            <label for="pass">PASSWORD</label><br />
                            <input type="password" name="pass" class="form-control" />
                        </div>

                        <div class="form-group">
                            <input type="submit" class="btn btn-default" class="form-control" id="submit" value="ADD ADMINISTRATOR" />
                        </div>

                    </form>
                </div>
            </div>';
    }
    
    
    include_once('../includes/footer.html');
    
}else{ //if page accessed in error - redirect and destroy cookies/sessions for security
    session_destroy();
    setcookie('PHPSESSID');
    $_SESSION=[];
    header('location:index.php');
}

?>
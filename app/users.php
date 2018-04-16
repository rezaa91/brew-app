<?php

session_start();

if(isset($_SESSION['id']) && $_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT'])){
    
    //include files
    $page_title = "Users";
    $css = '../src/css/users.css';
    include_once('../includes/header.html'); //header
    include_once('../includes/navigation.html'); //navigation bar
    include_once('../includes/db_connection.php'); //DB connection
    
    
    //create body content to display users in a table - needs ability to add and delete users
    echo '<div class="container" id="users-wrapper"><h3 class="users-title">Users</h3></div>'; //header
    
    //begin table
    echo '<div class="text-center">
        <table class="container">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Busy Level</th>
                    <th>Brew Counter</th>
                    <th>Drink Type</th>
                    <th>Milk</th>
                    <th>Sugar</th>
                </tr>
            </thead>
            <tbody>
                <tr>';
    
    
    //enter users in to table
    $q = "SELECT u.drink_id, u.first_name, u.surname, u.email, u.brew_tally, d.drink_id, d.type, d.sugar, d.milk FROM users AS u LEFT JOIN drink AS d ON u.drink_id = d.drink_id ORDER BY u.brew_tally DESC";
    $r = mysqli_query($dbc, $q);
    
    if($r){ //if successful - store data in table
        while($row = mysqli_fetch_array($r)){
            echo "<tr>
                <td>{$row['first_name']}</td>
                <td>{$row['surname']}</td>
                <td>{$row['email']}</td>
                <td>default</td>
                <td>{$row['brew_tally']}</td>
                <td>{$row['type']}</td>
                <td>{$row['milk']}</td>
                <td>{$row['sugar']}</td>
            </tr>";
        }
        
        
    }else{
        echo "Error: ". mysqli_error($dbc);
    }
    echo "</tr></tbody></table></div>"; //close remainder of table and body content
    
    
    
    //handle add user form
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        //validate form
        $errors=[]; //initiate errors array
        
        #firstname
        if(isset($_POST['first_name']) && !empty($_POST['first_name'])){
            $first_name = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
        }else{
            $first_name = null;
            $errors[] = "Please enter the users first name";
        }
        
        #lastname
        if(isset($_POST['last_name']) && !empty($_POST['last_name'])){
            $last_name = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
        }else{
            $last_name = null;
            $errors[] = "Please enter the users last name";
        }
        
        #email
        if(isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
        }else{
            $email = null;
            $errors[] = "Please enter the users email";
        }
        
        
        #busy
        if(isset($_POST['busy']) && is_numeric($_POST['busy'])){
            $busy = $_POST['busy'];
        }else{
            $busy = null;
            $errors[] = "Please select how busy the user is";
        }
        
        #drink type
        if(isset($_POST['type']) && !empty($_POST['type'])){
            $type = mysqli_real_escape_string($dbc, trim($_POST['type']));
        }else{
            $type = null;
            $errors[] = "Please enter the users preference of drink";
        }
        
        #milk
        if(isset($_POST['milk']) && is_numeric($_POST['milk'])){
            $milk = $_POST['milk'];
        }else{
            $milk = null;
            $errors[] = "Please state whether the user would like milk";
        }
        
        #sugar
        if(isset($_POST['sugar']) && is_numeric($_POST['sugar'])){
            $sugar = $_POST['sugar'];
        }else{
            $sugar = null;
            $errors[] = "Please enter the users sugar preference";
        }
        
        //if no errors - add user, only if meets DB requirements - else inform user
        if(!$errors){
            
            $q = "SELECT email FROM users WHERE email='$email'";//if email already exists
            $r = mysqli_query($dbc,$q);
            
            if(mysqli_num_rows($r) == 0){ //if email does not exist in database currently, add user to database
                $q = "SELECT drink_id FROM drink WHERE type='$type' && sugar='$sugar' && milk='$milk'";
                $r = mysqli_query($dbc, $q);
                
                if($r){
                    $drink_id = mysqli_fetch_array($r)['drink_id']; //store drink_id in var to store in DB
                    $q = "INSERT INTO users(drink_id, first_name, surname, email, brew_tally)VALUES($drink_id, '$first_name','$last_name','$email',0)";
                    $r = mysqli_query($dbc, $q);
                    
                    if(mysqli_affected_rows($dbc) == 1){//if inserted in to DB correctly
                        echo "<div class='container text-center color'>The user has been added.</div>";
                    }else{
                        echo "<div class='container text-center color'>Sorry, something went wrong. Please try again.</div>";
                    }
                    
                }else{
                    echo mysqli_error($dbc);
                }
                
            }else{
                echo "<div class='container text-center color'>Email already exists, please choose another.</div>";
            }
            
            
        }else{
            $error_msg = "<div class='error-msg'><span class='error-header'>Sorry, we could not add the user for the following reasons:</span><ul>";
            foreach($errors as $err){
                $error_msg.= "<li>$err</li>";
            }
            $error_msg.= "</ul></div>";
        }
                            
                            
    }
    
    
    //create form so administrator able to add users
    echo '<div class="container" id="add-user-wrapper">
    <div class="row">
        <div class="col-md-6">
            <h4 class="users-title">Add User</h4>
            <form role="form" action="users.php" method="post" id="add-user-form">
                <div class="form-group">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" />
                </div>

                <div class="form-group">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" />
                </div>

                <div class="form-group">
                    <input type="text" name="email" class="form-control" placeholder="Email" />
                </div>

                <div class="form-group">
                    <select name="busy" class="form-control">
                        <option value="" disabled selected>How Busy Are You?</option>
                        <option value="0">Not at all busy</option>
                        <option value="1">Moderate</option>
                        <option value="2">Snowed in!</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="type" class="form-control">
                        <option value="" disabled selected>Drink Preference</option>
                        <option value="tea">Tea</option>
                        <option value="coffee">Coffee</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="sugar" class="form-control">
                        <option value="" disabled selected>Sugar?</option>
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="milk">Milk: </label><br />
                    <input type="radio" name="milk" value="1"/> Yes
                    <input type="radio" name="milk" value="0"/> No
                </div>

                <div class="form-group">
                    <input type="submit" class="btn form-control" id="add-user-btn" value="ADD USER" />
                </div>
            </form>
        </div>
        <div class="col-md-6">';
    if(isset($error_msg)){echo $error_msg;};
    echo '</div></div></div>';//end of form
    
    
    
    include_once('../includes/footer.html'); //include footer
    
    
}else{//page accessed in error
    session_destroy();
    setcookie('PHPSESSID');
    $_SESSION=[];
    header('location:index.php'); //redirect user to index after destroying session
}


?>
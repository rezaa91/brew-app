<?php

session_start();


//include page title and header
$css = '../src/css/login.css';
$page_title = "LOGIN";
include_once('../includes/header.html');


?>


<!--create form-->
<div class="container" id="form-wrapper">
    <h2 class="text-primary text-center">SIGN IN</h2>
    <form role="form" action="index.php" method="post">
        <div class="form-group">
            <label for="email">EMAIL:</label><br />
            <input type="text" name="email" class="form-control" />
        </div>
        
        <div class="form-group">
            <label for="pass">PASSWORD:</label><br />
            <input type="password" name="pass" class="form-control" />
        </div>
        
        <div class="form-group">
            <input type="submit" name="submit" class="form-control btn btn-lg btn-primary" value="LOGIN"/>
        </div>
        
    </form><!--end of form-->
</div><!--end of form wrapper-->


<!--display errors if previous unsuccessful login attempt-->
<div id="errors" class="container">
    <?php
    
    if(isset($_SESSION['errors'])){
        echo "<h5 class='text-danger'>Sorry, we could not login you in due to the following reasons:</h5><ul class='list-group list-group-flush'>";
        foreach($_SESSION['errors'] as $err){
            echo '<li class="list-group-item text-muted">'.$err.'</li>';
        }
        echo "</ul>";
    }
    
    
    ?>
</div>






<?php
//include footer
include_once('../includes/footer.html');
?>
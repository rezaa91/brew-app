<?php

session_start();

//only display page if user logged in
if(isset($_SESSION['id']) && $_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT'])){
    
    //include necessary files
    $page_title = "Call Round";
    $css = '../src/css/call_round.css';
    include_once('../includes/header.html');
    include_once('../includes/navigation.html');
    
    echo '<div class="container" id="call-round-wrapper">
        <h3 class="text-center call-round-title">Time for you to<br/><span class="just">just</span> <span class="brew-it">BREW IT...</span></h3>';
    
    
    //include database in order to choose user at random
    include_once('../includes/db_connection.php');
    $q = "SELECT * FROM users ORDER BY RAND() LIMIT 1"; //select user at random
    $r = mysqli_query($dbc, $q);
    
    if($r){ //if query ran ok
        while($row = mysqli_fetch_array($r)){
            echo "<h3 class='text-center call-round-user'>". strtoupper($row['first_name'].' '.$row['surname'])."</h3>";
            $cur_tally = $row['brew_tally']; //get current tally that user has been called to make the brews
            
            $q = "UPDATE users SET brew_tally=".($cur_tally+1)." WHERE email='".$row['email']."'";
            $update = mysqli_query($dbc, $q);
            
            if(!$update){ //if update failed then inform user, else send email to user to make the brews for everyone
                echo mysqli_error($dbc);
            }else{
                //send email to user
                $to = $row['email']; //users email
                $subject = "Time for you to make the brews!"; //default subject
                
                //create the body which shows the tea list
                $body = '<div class="container" id="table-wrapper"><h3>Orders:</h3><table class="text-center container-fluid">
                    <thead>
                        <tr>
                            <th>first name</th>
                            <th>last name</th>
                            <th>type of drink</th>
                            <th>milk</th>
                            <th>sugar</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                //get the tea list
                $q = "SELECT u.drink_id, u.first_name, u.surname, d.drink_id, d.type, d.milk, d.sugar FROM users AS u INNER JOIN drink AS d ON u.drink_id=d.drink_id";
                $tea_query = mysqli_query($dbc, $q);
                
                if($tea_query){
                    while($teaList = mysqli_fetch_array($tea_query)){
                        $body .= "<tr>
                            <td>{$teaList['first_name']}</td>
                            <td>{$teaList['surname']}</td>
                            <td>{$teaList['type']}</td>
                            <td>{$teaList['milk']}</td>
                            <td>{$teaList['sugar']}</td>
                        </tr>";
                    }
                    $body .= "</tbody></table></div>";//close the table
                }else{
                    echo mysqli_error($dbc);
                }
                
                $body = wordwrap($body, 70);
                $headers = "MIME-VERSION:1.0" ."\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" ."\r\n";
                $headers .= 'FROM: <info@doman.co.uk>'; //SET THIS TO SERVER DOMAIN EMAIL IN ORDER TO SEND EMAIL TO RECIPIENT
                
                
                //send email to picked user
                @mail($to, $subject, $body, $headers);
                
                //display the table on the page
                echo $body;
                echo "<div class='container email-msg'><h5>The user has been emailed. Now sit back and await your brews.</h5></div>";
                
            }
        }
        
        
    }else{ //if unable to select a random user
        echo mysqli_error($dbc);
    }
    
    echo '</div>';
    
    
    
    include_once('../includes/footer.html');
    
}else{//redirect user if accessed in error
    //destroy session, if any
    session_destroy();
    $_SESSION=[];
    setcookie('PHPSESSID');
}

?>
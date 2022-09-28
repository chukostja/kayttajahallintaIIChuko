<?php
    include('./config/db.php');

    function myFilter($arvo){
      global $connection;  
      return mysqli_real_escape_string($connection,strip_tags($arvo));      
    }

    global $email_verified, $email_already_verified, $activation_error;
    if(!empty($_GET['token'])){
       $token = myFilter($_GET['token']);
    } else {
        $token = "";
    }

    if($token != "") {
        $query = "SELECT is_active FROM users WHERE token = '$token'";
        $result = mysqli_query($connection, $query);
        $countRow = mysqli_num_rows($result);

        if($countRow == 1){
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                $is_active = $row['is_active'];
                if($is_active == 0) {
                    $query = "UPDATE users SET is_active = '1' WHERE token = '$token'";
                    $update = mysqli_query($connection, $query);
                    if($update){
                        $email_verified = '<div class="alert alert-success">
                            User email successfully verified!
                            </div>';
                       }
                  } else {
                        $email_already_verified = '<div class="alert alert-danger">
                            User email already verified!
                            </div>';
                  }
            }
        } else {
            $activation_error = '<div class="alert alert-danger">
                Activation error!
                </div>';
        }
    }

?>
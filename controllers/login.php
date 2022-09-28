<?php
   
    // Database connection
    include('config/db.php');
    include('debuggeri.php');
    $passwordRegex = "/^.{16,}$/";
    $defaultPage = "./dashboad.php";

    global $wrongPwdErr, $accountNotExistErr, $emailPwdErr, $verificationRequiredErr, $email_empty_err, $pass_empty_err;
    debuggeri($_POST);
    if(isset($_POST['login'])) {

    $email_signin    = $_POST['email_signin'];
    $password_signin = $_POST['password_signin'];
    if(!empty($email_signin) && !empty($password_signin)){
        $user_email = filter_var($email_signin, FILTER_SANITIZE_EMAIL);
        $pswd = mysqli_real_escape_string($connection, $password_signin);
        $query = "SELECT password,is_active FROM users WHERE email = '$email_signin'";
        $result = mysqli_query($connection, $query);
        $rowCount = mysqli_num_rows($result);

        if(!$result){
           die("SQL query failed: " . mysqli_error($connection));
        }
            
        if($rowCount <= 0) {
            $accountNotExistErr = 
                '<div class="alert alert-danger">
                User account does not exist.
                </div>';
        } else {
            list($password_hash,$is_active) = mysqli_fetch_row($result); 
            if (!password_verify($password_signin, $password_hash)){
                $emailPwdErr = 
                '<div class="alert alert-danger">
                Either email or password is incorrect.
                </div>';
                }

            else {
                if ($is_active == '1') {
                        $_SESSION['loggedin'] = true;
                        if (isset($_SESSION['next_page'])){
                            $next_page = $_SESSION['next_page'];
                            unset($_SESSION['next_page']);
                            header("location: $next_page");
                            exit;
                            }
                        header("Location: $defaultPage");
                }   
     
                else {
                    $verificationRequiredErr = '<div class="alert alert-danger">
                        Account verification is required for login.
                        </div>';
                }
            }

            }

        } else {
            if(empty($email_signin)){
                $email_empty_err = 
                    "<div class='alert alert-danger email_alert'>
                    Anna käyttäjätunnus.
                    </div>";
            }
            
            if(empty($password_signin)){
                $pass_empty_err = 
                    "<div class='alert alert-danger email_alert'>
                    Anna salasana.
                    </div>";
            }            
        }
    }

?>    
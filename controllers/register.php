<?php
   
    // Database connection
    include('config/db.php');
    include('debuggeri.php');
    include('posti.php');  
    // Error & success messages
    global $success_msg, $email_exist, $f_NameErr, $l_NameErr, $_emailErr, $_mobileErr, $_passwordErr;
    global $fNameEmptyErr, $lNameEmptyErr, $emailEmptyErr, $mobileEmptyErr, $passwordEmptyErr, $email_verify_err, $email_verify_success;
    $passwordRegex = "/^.{16,}$/";

    // Set empty form vars for validation mapping
    $_firstname = $_lastname = $_email = $_mobilenumber = $_password = "";

    if(isset($_POST["submit"])) {
        $firstname     = $_POST["firstname"];
        $lastname      = $_POST["lastname"];
        $email         = $_POST["email"];
        $mobilenumber  = $_POST["mobilenumber"];
        $password      = $_POST["password"];

        // check if email already exist
        $_email = mysqli_real_escape_string($connection, $email);
        $query = "SELECT 1 FROM users WHERE email = '$_email'"; 
        $result = mysqli_query($connection, $query);
        $userExists = mysqli_num_rows($result);

        // Verify if form values are not empty
        if(!empty($firstname) && !empty($lastname) && !empty($email) && !empty($mobilenumber) && !empty($password)){
       
            // check if user email already exist
            if($userExists) {
                $email_exist = '
                    <div class="alert alert-danger" role="alert">
                        User with email already exist!
                    </div>';
            } else {
                // clean the form data before sending to database
                $_firstname = mysqli_real_escape_string($connection, $firstname);
                $_lastname = mysqli_real_escape_string($connection, $lastname);
                $_mobilenumber = mysqli_real_escape_string($connection, $mobilenumber);
                $_password = mysqli_real_escape_string($connection, $password);
                $validated = true;
                if(!preg_match("/^[a-zA-Z ]*$/", $_firstname)) {
                    $validated = false;
                    $f_NameErr = '<div class="alert alert-danger">
                            Only letters and white space allowed.
                        </div>';
                }
                if(!preg_match("/^[a-zA-Z ]*$/", $_lastname)) {
                    $validated = false;
                    $l_NameErr = '<div class="alert alert-danger">
                            Only letters and white space allowed.
                        </div>';
                }
                if(!filter_var($_email, FILTER_VALIDATE_EMAIL)) {
                    $validated = false;
                    $_emailErr = '<div class="alert alert-danger">
                            Email format is invalid.
                        </div>';
                }
                if(!preg_match("/^[0-9]{7,15}+$/", $_mobilenumber)) {
                    $validated = false;
                    $_mobileErr = '<div class="alert alert-danger">
                        Väärän pituinen numero
                        </div>';
                }
                if(!preg_match($passwordRegex, $_password)) {
                    $validated = false;
                    $_passwordErr = '<div class="alert alert-danger">
                        Anna vähintään 16 merkkiä pitkä salasana.
                        </div>';
                }
                
                if($validated){
                    // Generate random activation token
                    $token = md5(rand().time());
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);
                    $query = "INSERT INTO users (firstname, lastname, email, mobilenumber, password, token, is_active) VALUES ('$_firstname', '$_lastname', '$_email', '$_mobilenumber', '$password_hash', 
                        '$token', '0')";
                    $result = mysqli_query($connection, $query);
                    if(!$result){
                        die("MySQL query failed!" . mysqli_error($connection));
                    } 

                    // Send verification email
                    if($result) {
                        $msg = 'Click on the activation link to verify your email. <br><br>
                          <a href="http://localhost/php-user-authentication/user_verification.php?token='.$token.'"> Click here to verify email</a>
                        ';
                        $topic = 'Please Verify Email Address!';
                        $tulos = posti($email,$msg,$topic);    
  
                        if(!$tulos){
                            $email_verify_err = '<div class="alert alert-danger">
                                    Verification email coud not be sent!
                            </div>';
                        } else {
                            $email_verify_success = '<div class="alert alert-success">
                                Verification email has been sent!
                            </div>';
                        }
                    }
                }
            }
        } else {
            if(empty($firstname)){
                $fNameEmptyErr = '<div class="alert alert-danger">
                    First name can not be blank.
                </div>';
            }
            if(empty($lastname)){
                $lNameEmptyErr = '<div class="alert alert-danger">
                    Last name can not be blank.
                </div>';
            }
            if(empty($email)){
                $emailEmptyErr = '<div class="alert alert-danger">
                    Email can not be blank.
                </div>';
            }
            if(empty($mobilenumber)){
                $mobileEmptyErr = '<div class="alert alert-danger">
                    Mobile number can not be blank.
                </div>';
            }
            if(empty($password)){
                $passwordEmptyErr = '<div class="alert alert-danger">
                    Password can not be blank.
                </div>';
            }            
        }
    }
?>
<?php

require_once 'connection.php';

class login
{

    public static function check_login()
    {

        $connection = \connection::initilize_connection();

        $return_arr = [];

        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = 'Please enter username.';
        } else{
            $username = trim($_POST["username"]);
            $username = mysqli_real_escape_string($connection,$username);
        }

        // Check if password is empty
        if(empty(trim($_POST['password']))){
            $password_err = 'Please enter your password.';
        } else{
            $password = trim($_POST['password']);
            $password = mysqli_real_escape_string($connection,$password);
        }

        if(empty($username_err) && empty($password_err)){

            $sql = "SELECT user_id ,username, password FROM users WHERE username = ?";

            if($stmt = $connection->prepare($sql)){
                // binding username variable
                $stmt->bind_param("s", $param_username);

                $param_username = $username;

                // execute statement
                if($stmt->execute()){
                    // Store result
                    $stmt->store_result();

                    // Check if username exists, if yes then verify password
                    if($stmt->num_rows == 1){
                        // binding username and password variables
                        $stmt->bind_result($user_id, $username, $hashed_password);
                        if($stmt->fetch()){
                            if(password_verify($password, $hashed_password)){
                                session_start();
                                $_SESSION['user_id'] = $user_id;
                                $_SESSION['username'] = $username;

                                if ($_POST['remember_me'] == "on")
                                {
                                    $year = time() + 31536000;
                                    setcookie('remember_username', $username, $year);
                                    setcookie('remember_password', $password, $year);
                                }

                                header("location: categories.php");
                            } else{
                                // Display an error message if password is not valid
                                $password_err = 'Invalid Credentials !';
                                $return_arr["password_err"] = $password_err;
                            }
                        }
                    } else{
                        // Display an error message if username doesn't exist
                        $username_err = 'Invalid Credentials !';
                        $return_arr["username_err"] = $username_err;
                    }
                } else{
                    $return_arr["error_msg"] = "Oops! Something went wrong. Please try again later.";
                }
            }

            // Close statement
            $stmt->close();

        }
        else{

            $return_arr["username_err"] = $username_err;
            $return_arr["password_err"] = $password_err;
        }


        // Close connection
        mysqli_close($connection);

        $return_arr["username"] = $username;

        return $return_arr;

    }
}
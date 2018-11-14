<!--
    COS80001 - Web Application Development
    Assignment 1 - login.php
    Student name: Michele Peghini
    Student ID: 101940042

    This file contains the form that allows a registered user to login to the website,
    after successful login, the user is automatically redirected to the boking page.
    User email is stored in $_SESSION['user_email'] variable on server and can be accessed in the booking.php script
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CabsOnline - Login</title>
    <meta name="author" content="Michele Peghini - 101940042">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav id="main-navigation">
        <h1>CabsOnline</h1>
        <span><a href="#">Login</a></span>
        <span><a href="register.php">Register</a></span>
    </nav>
    <form id="login-form">
        <legend>Login</legend>
        <fieldset>
            <div class="row">
                <label for="user-email">Email:</label>
                <input type="text" pattern="([A-Za-z0-9_.-]{1,})@([a-z]{1,})(\.[a-z]{1,})+" id="user-email" name="user-email" maxlength="50" required>
            </div>
            <div class="row">
                <label for="user-pwd">Password:</label>
                <input type="password" id="user-pwd" name="user-pwd" maxlength="50" required>
            </div>

            <p class="form-link">Not registered yet? <a href="register.php">Register here!</a></p>

            <input type="submit" value="Login!">
        </fieldset>
    </form>
</body>
<?php
    // import variables for db connection and utility functions
    require_once ('utility.php');

    // check all form fields have been submitted
    if (isset($_GET['user-pwd'])&& isset($_GET['user-email']))
    {
        // store form values in variables, stripped of any whitespace
        $email = strtolower(trim($_GET['user-email']));//convert to lowercase
        $pwd = trim($_GET['user-pwd']);
        
        //validate form fields, validate() function declared in utility.php file
        if (!validate($email, 50, "/([a-z0-9_.-]{1,}@[a-z]{1,}\.[a-z.]{1,}){1}/"))
        {
             print_msg("Invalid Email!","error");
        }
        
        // connect to db. if connection error, store errorm message in $error_arr
        $connect = mysqli_connect($host, $db_user['name'], $db_user['pwd'], $db_name);
        if (!$connect)
        {
             print_msg(mysqli_connect_error(),"error");
        }

        // check email provided is unique
        $email = mysqli_escape_string($connect, $email);
        $login_email_query = "SELECT email FROM customer WHERE email='".$email."';";
        $results = mysqli_query($connect, $login_email_query);
        // no results, email not registered
        if (mysqli_num_rows($results) == 0)
        {
             print_msg("Email not registered!","error");
        }
        // multiple results, DB is inconsistent 
        elseif (mysqli_num_rows($results) > 1)
        {
             print_msg("DB error! Multiple users with same email, unable to Login!","error");
        }

        // check user with given email AND password 
        $login_password_query = "SELECT email FROM customer WHERE email='".$email."'AND customer_pwd='".$pwd."';";
        $results = mysqli_query($connect, $login_password_query);
        // query has no results, password provided is incorrect
        if (mysqli_num_rows($results) != 1)
        {
             print_msg("Incorrect password!","error");
        }
        // close connection
        mysqli_close($connect);

        // start session
        session_start();
        $_SESSION['user-email'] = $email;

        //redirect to booking
        redirect('booking');
    } 
?>
</html>
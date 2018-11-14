<!--
    COS80001 - Web Application Development
    Assignment 1 - register.php
    Student name: Michele Peghini
    Student ID: 101940042

    This file contains the form that allows a new user to register to the website,
    after successful registration, the user is automatically redirected to the boking page.
    User email is stored in $_SESSION['user_email'] variable on server and can be accessed in the booking.php script
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CabsOnline - Registration</title>
    <meta name="author" content="Michele Peghini - 101940042">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav id="main-navigation">
        <h1>CabsOnline</h1>
        <span><a href="login.php">Login</a></span>
        <span><a href="#">Register</a></span>
    </nav>
    <form id="registration-form">
        <legend>Registration</legend>
        <fieldset>
            <div class="row">
                <label for="user-name">Name:</label>
                <input type="text" pattern="[A-Za-z\s]{1,}" id="user-name" name="user-name" maxlength="50" required>
            </div>
            <div class="row">
                <label for="user-pwd">Password:</label>
                <input type="password" id="user-pwd" name="user-pwd" maxlength="50" required>
            </div>
            <div class="row">
                <label for="pwd-confirm">Confirm password:</label>
                <input type="password" id="pwd-confirm" name="pwd-confirm" maxlength="50" required>
            </div>
            <div class="row">
                <label for="user-email">Email:</label>
                <input type="text" pattern="(([A-Za-z0-9_.-]{1,}@[a-z]{1,}\.[a-z.]{1,}){1})+" id="user-email" name="user-email" maxlength="50" required>
            </div>
            <div class="row">
                <label for="user-phone">Phone:</label>
                <input type="tel" pattern="^0[0-9]{9}$" id="user-phone" name="user-phone" minlength="10" maxlength="10" required>
            </div>

            <p class="form-link">Already a member? <a href="login.php">Login here!</a></p>

            <input type="submit" value="Register!">
        </fieldset>
    </form>
</body>
<?php
    // import variables for db connection and utility functions
    require_once ('utility.php');

    // check all form fields have been submitted
    if (isset($_GET['user-name']) && isset($_GET['user-pwd']) && isset($_GET['pwd-confirm']) && isset($_GET['user-phone']) && isset($_GET['user-email']))
    {
        // store form values in variables, stripped of any whitespace
        $name = trim($_GET['user-name']);
        $pwd = trim($_GET['user-pwd']);
        $confirm = trim($_GET['pwd-confirm']);
        $email = strtolower(trim($_GET['user-email']));//convert to lowercase
        $phone = trim($_GET['user-phone']);
        
        // check pwd and confirm are the same 
        if (strcmp($pwd, $confirm) != 0)
        {
             print_msg("Passwords do not match!");
        }

        //validate form fields, validate() function declared in utility.php file
        if (!validate($name, 50, "/^[A-Za-z\s]{1,}$/"))
        {
             print_msg("Invalid Name!","error");
        }
        if (!validate($email, 50, "/([a-z0-9_.-]{1,}@[a-z]{1,}\.[a-z.]{1,}){1}/"))
        {
             print_msg("Invalid Email!","error");
        }
        if (!validate($phone,10,"/^0[0-9]{9}$/"))
        {
             print_msg("Invalid Phone!","error");
        }
        

        // connect to db. if connection error, call  print_msg() 
        $connect = mysqli_connect($host, $db_user['name'], $db_user['pwd'], $db_name);
        if (!$connect)
        {
             print_msg(mysqli_connect_error(),"error");
        }

        // check email provided is unique
        $email = mysqli_escape_string($connect, $email);
        $get_email_query = "SELECT email FROM customer WHERE email='".$email."';";
        $results = mysqli_query($connect, $get_email_query);
        // query has results, email provided is already registered
        if (mysqli_num_rows($results) != 0)
        {
             print_msg("Email already registered!","error");
        }
        
        // build query and insert data into db
        $insert_user_query = "INSERT INTO customer (email, customer_name, customer_pwd, phone_number) VALUES ('$email','$name','$pwd','$phone')";
        $results = mysqli_query($connect, $insert_user_query);
        // query error
        if (!$results)
        {
             print_msg(mysql_error($connect),"error");
        } 
        else
        {
            // close connection
            mysqli_close($connect);
    
            // start session
            session_start();
            $_SESSION['user-email'] = $email;
            redirect("booking");
        }
    } 
?>
</html>
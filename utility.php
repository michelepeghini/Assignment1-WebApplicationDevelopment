<?php
/* COS80001 - Web Application Development
* Assignment 1 - cabsonline.sql
* Student name: Michele Peghini
* Student ID: 101940042
*
* This file stores:
* - user name and password for the  database user 's101940042', as associative array.
* - $host and a $db_name variables, used to store host address and name of the database.
* 
* This file is meant to be imported into scripts that require db connectivity, 
* providing thus a centralized repository for db users and a reusable PDO object,
* avoiding its declaration in every script. 
*/

/* Associative array, stores name and password for 'default_user' on 'cabsonline'.
 * used for db connections in scripts 
 */
$db_user = array(
    'name' => 'root', //'cabsonline_user',
    'pwd' => ''//'user_123'
 );

// name of the host server
$host = "localhost"; //feenix-mariadb.swin.edu.au

// name of the db
$db_name = "cabsonline";

/*
* Utility function, validates a form field.
* 
* This function provides server-side validation of form fields, in addition to client-side validation performed before submission.
* Checks for:
* - empty string,
* - exceeds character limit
* - has forbidden characters, i.e. does not match regular expression or matches more than once
* 
* @param string $field the form field to be validated as a string
* @param int $max_length the max length of the field, based on db constraints
* @param string $regex the regular expression $field will be valideted against
* 
* @return boolean true if string is valid, false if any of the check fails
*/
function validate($field, $max_length, $regex)
{
    // cache length of form field and check if empty or if too long
    $length = strlen($field);
    if ($length == 0 || $length > $max_length)
    {
        return false;
    }

    // check field against $regex, if no match or more than one match return false
    preg_match_all($regex,$field,$match_arr);
    if (count($match_arr[0]) != 1)
    {
        return false;
    }

    return true;
}

/* Utility function, prints messages to output buffer.
 * 
 * Used to display either 'success' or 'error' messages.
 * $msg_type is added as class in the HTML elements of the message. Though CSS, this will determine the color of the message (i.e. error = red, success = green)
 * 
 * @param string $message the text of the message to be displayed
 * @param string $msg_type the type of message, either 'success' or 'error'. Parameter determines CSS classes associated with created HTML elements. Defaults to empty string.
 * @param string $exit_flag determines whether to exit the script after outputting the message. 0 = continue, 1 = exit. Defaults to 1.
 */
function print_msg($message, $msg_type = '', $exit_flag = 1)
{
    echo "<div class='results $msg_type'><p class='$msg_type'>$message</p></div>";
    if ($exit_flag == 1)
    {
        exit();
    }
}

/* Utility function, redirects the user to a specified location.
 * 
 * Used in login and register scripts to redirect to booking.
 * Used in booking to redirect to login, if user tries to access the page while not logged in.
 * 
 * @param string $location the location of redirection, matched against an internal associative array.
 */
function redirect($location)
{
    $location_arr = array(
        'booking' => '/booking.php',
        'login' => '/login.php',
        'register' => '/register.php'
    );
    
    $redirect_arr = explode("/",$_SERVER['PHP_SELF']);
    if (array_pop($redirect_arr) != NULL)
    {
        $redirect_url = implode("/",$redirect_arr) . $location_arr["$location"];
    }
    else
    {
         print_msg("Server error! Unable to redirect!","error");
    }
    
    // redirect to bookings.php
    header("Location: $redirect_url");
    exit();
}

<!--
    COS80001 - Web Application Development
    Assignment 1 - booking.php
    Student name: Michele Peghini
    Student ID: 101940042

    This file contains the form that allows a loggedin user to make a booking
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CabsOnline - Booking</title>
    <meta name="author" content="Michele Peghini - 101940042">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
    require_once("utility.php");
    //start session
    session_start();
    // check if user is logged in, otherwise redirect to login.php
    if(!isset($_SESSION['user-email']) /*|| !isset($_SESSION['user-name'])*/)
    {
        redirect("login");
    }
    //store user date from  SESSION in variables
    $user_email = $_SESSION['user-email'];
    //$user_name = $_SESSION['user-name'];

    // create current date and time to set as a default value for pu-date and pu-time fields
    // fields can still be edited by user and will be validated after form submission
    $current_date = date_create_from_format("d-m-Y",date("d-m-Y"));
    $current_time = date_create_from_format("H:i",date("H:i"));
    $min_booking_interval = new DateInterval("PT1H");
    //modifies time to be 1 hour after current time
    $current_time->add($min_booking_interval);
?>
<nav id="main-navigation">
        <h1>CabsOnline</h1>
        <span> Logged in as: <span class="main-navigation--username"><?php echo $_SESSION['user-email']?></span></span>
    </nav>
    <form id="booking-form">
        <legend>Booking</legend>
        <fieldset>
            <div class="row">
                <label for="passenger-name">Passenger name:</label>
                <input type="text" pattern="^[A-Za-z\s]{1,}$" id="passenger-name" name="passenger-name" maxlength="50" required>
            </div>
            <div class="row">
                <label for="passenger-phone">Passenger phone:</label>
                <input type="text" pattern="0[0-9]{9}" id="passenger-phone" name="passenger-phone" minlength="10" maxlength="10" required>
            </div>
            <div class="form-group">
                <h3 class="form-group--label">Pick-up address</h3>
                <div class="form-group--row">
                    <label for="unit-number">Unit number:</label>
                    <input type="number" id="unit-number" name="unit-number" min="1"  step="1" placeholder="-">
                </div>
                <div class="form-group--row">
                    <label for="street-number">Street number:</label>
                    <input type="number" id="street-number" name="street-number" min="1" step="1" required>
                </div>
                <div class="form-group--row">
                    <label for="street-name">Street name:</label>
                    <input type="text" pattern="^[A-Za-z\s]{1,}$" id="street-name" name="street-name" maxlength="50" required>
                </div>
                <div class="form-group--row">
                    <label for="suburb">Suburb:</label>
                    <input type="text" pattern="^[A-Za-z\s]{1,}$" id="suburb" name="suburb" maxlength="50" required>
                </div>
            </div>
            <div class="row">
                <label for="destination">Destination suburb:</label>
                <input type="text" pattern="^[A-Za-z\s]{1,}$" id="destination" name="destination" maxlength="50" required>
            </div>
            <div class="row">
                <label for="pu-date">Pick-up date:</label>
                <input type="text" pattern="^\d{1,2}-\d{1,2}-\d{4}$" id="pu-date" name="pu-date" minlength="8" maxlength="10" value="<?php echo date_format($current_date, "d-m-Y")?>" required>
            </div>
            <div class="row">
                <label for="pu-time">Pick-up time:</label>
                <input type="text" pattern="^\d{1,2}:\d\d$" id="pu-time" name="pu-time" minlength="4" maxlength="5" value="<?php echo date_format($current_time, "H:i")?>" required>
            </div>
            <input type="submit" value="Book the ride!">
        </fieldset>
    </form>
    <?php
    if (isset($_GET['passenger-name']) && isset($_GET['passenger-phone']) && isset($_GET['street-number']) && isset($_GET['street-name']) && isset($_GET['suburb']) && isset($_GET['destination']) && isset($_GET['pu-date']) && isset($_GET['pu-time']))
    {
        // store form fields into variables, trimmed of whitespace
        $passenger_name = trim($_GET['passenger-name']);
        $passenger_phone = trim($_GET['passenger-phone']);
        $pu_street_no = trim($_GET['street-number']);
        $pu_street_name = trim($_GET['street-name']);
        $pu_suburb = trim($_GET['suburb']);
        $destination_suburb = trim($_GET['destination']);
        $pu_date = trim($_GET['pu-date']);
        $pu_time = trim($_GET['pu-time']);
        $pu_unit_no = null;
        if (isset($_GET['unit-number']) && $_GET['unit-number'] != "")
        {
            $pu_unit_no = trim($_GET['unit-number']);
            if (!validate($pu_unit_no, 5, "/^[0-9]{1,}$/"))
            {
                 print_msg("Invalid unit number!", "error");
            }
        }
        
        // Validate fields 
        if (!validate($passenger_name, 50, "/^[A-Za-z\s]{1,}$/"))
        {
             print_msg("Invalid name!", "error");
        }

        if (!validate($passenger_phone, 10, "/^0[0-9]{9}$/"))
        {
             print_msg("Invalid phone!", "error");
        }

        if (!validate($pu_street_no, 6, "/^[0-9]{1,6}$/"))
        {
             print_msg("Invalid street number!", "error");
        }

        if (!validate($pu_street_name, 50, "/^[A-Za-z\s]{1,}$/"))
        {
             print_msg("Invalid street name!", "error");
        }

        if (!validate($pu_suburb, 50, "/^[A-Za-z\s]{1,}$/"))
        {
             print_msg("Invalid suburb!", "error");
        }
        
        if(!validate($destination_suburb, 50, "/^[A-Za-z\s]{1,}$/"))
        {
             print_msg("Invalid destination!", "error");
        }

        // create new exception to throw when date formatting is wrong
        class DateFormatException extends Exception {};
        // Validate pu-date
        try 
        {
            if(!validate($pu_date, 10, "/^\d{1,2}-\d{1,2}-\d{4}$/"))
            {
                throw new DateFormatException();
            } 
            
            // use checkdate to ensure $pu_date is in valid format. 
            // i.e not 32-22-2018, which would still be accepted by date_create_from_format()
            $date_arr = explode("-", $pu_date);
            if (!checkdate((int)$date_arr[1], (int)$date_arr[0], (int)$date_arr[0]))
            {
                throw new DateFormatException();
            }
            
            // check that date_create_from_format successfully returned a DateTime object    
            $pu_date = date_create_from_format("d-m-Y", $pu_date);
            if ($pu_date == false)
            {
                throw new DateFormatException();
            }    
        }
        // catch DateFormatException or any other and print erro message
        catch(DateFormatException $e)
        {
             print_msg("Invalid date format!", "error");
        }

        // create new exception to throw when time formatting is wrong
        class TimeFormatException extends Exception {};
        // Validate pu-time
        try
        {
            if (!validate($pu_time, 5, "/^\d{1,2}:\d\d$/"))
            {
                throw new TimeFormatException();
            }

            // ensure $pu_time is in valid format. 
            // i.e not 26:30, which would still be accepted by date_create_from_format() as 02:30
            $time_arr = explode(":", $pu_time);
            if ((int)$time_arr[0] > 23 || (int)$time_arr[1] > 59)
            {
                throw new TimeFormatException();
            }
            
            // check that date_create_from_format successfully returned a DateTime object    
            $pu_time = date_create_from_format("H:i", $_GET['pu-time']);
            if ($pu_time == false)
            { 
                throw new TimeFormatException();
            }
        }
        catch (TimeFormatException $e)
        {
             print_msg("Invalid time format!", "error");
        }

        // store booking date and time
        $booking_date = date_create_from_format("d-m-Y",date("d-m-Y"));
        $booking_time = date_create_from_format("H:i",date("H:i"));
        
        // check that pickup date is not prior to booking date
        if ($pu_date < $booking_date)
        {
             print_msg("Invalid pickup date. Pickup date cannot be in the past!", "error");
        } 
        // booking date and pickup date are the same
        elseif ($pu_date == $booking_date)
        {
            // get interval between booking and pickup time
            $pu_interval = $booking_time->diff($pu_time);
            // check interval is not negative (i.e. pickup time is in the past) OR interval is less than 1 hour 
            if (($pu_interval->invert == 1) || $pu_interval < $min_booking_interval)
            {
                 print_msg("Pick up time must be at least 1 hour after the booking time!", "error");
            }
        }
        
        // convert dates and times to strings in db format
        $pu_date_str = date_format($pu_date, "Y-m-d");
        $pu_time_str = date_format($pu_time, "G:i:s");
        $booking_date_str = date_format($booking_date, "Y-m-d");
        $booking_time_str = date_format($booking_time, "H:i:s");

        
        // connect to db. if connection error, call  print_msg() 
        $connect = mysqli_connect($host, $db_user['name'], $db_user['pwd'], $db_name);
        if (!$connect)
        {
             print_msg(mysqli_connect_error(), "error");
        }
        
        // insert datsa into db
        if ($pu_unit_no != null)
        {
            $insert_booking_query = "INSERT INTO booking(email_address, passenger_name, passenger_phone, pu_unit_no, pu_street_no, pu_street_name, pu_suburb, destination_suburb, pu_date, pu_time, booking_date, booking_time) VALUES ('$user_email', '$passenger_name', '$passenger_phone', $pu_unit_no, $pu_street_no, '$pu_street_name', '$pu_suburb', '$destination_suburb', '$pu_date_str', '$pu_time_str', '$booking_date_str', '$booking_time_str')";
        }
        else
        {
            $insert_booking_query = "INSERT INTO booking(email_address, passenger_name, passenger_phone, pu_street_no, pu_street_name, pu_suburb, destination_suburb, pu_date, pu_time, booking_date, booking_time) VALUES ('$user_email', '$passenger_name', '$passenger_phone', $pu_street_no, '$pu_street_name', '$pu_suburb', '$destination_suburb', '$pu_date_str', '$pu_time_str', '$booking_date_str', '$booking_time_str')";
        }
        
        $results = mysqli_query($connect, $insert_booking_query);
        // query error
        if (!$results)
        {
             print_msg(mysql_error($connect), "error");
        } 
        else
        {
            // get id of last row inserted;
            $booking_id = mysqli_insert_id($connect);
            
            // print response message
            
            print_msg("Thank you! Your booking reference number is $booking_id. We will pick up the passengers in front of your provided address at $pu_time_str on $pu_date_str.", "success", 0);

            // close connection
            mysqli_close($connect);

            // send email
            $to = $user_email;
            $subject =  "Your booking request with CabsOnline!";
            $message = "Dear $passenger_name, Thanks for booking with CabsOnline! Your booking reference number is $booking_id. We will pick up the passengers in front of your provided address at $pu_time_str on $pu_date_str.";
            $headers = "From: booking@cabsonline.com.au";
            mail($to, $subject, $message, $headers, "-r101940042@student.swin.edu.au -f101940042@student.swin.edu.au");
        }
    }
    ?>
</body>
</html>
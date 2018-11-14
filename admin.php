<!--
    COS80001 - Web Application Development
    Assignment 1 - admin.php
    Student name: Michele Peghini

    This file allows admins to display all 'unassigned' bookings with booking time that is less than 2 hours from current time.
    it also allows admins to update the status of a booking from 'unassigned to 'assigned' by inputing a booking number in the assign form and click 'Update' button.
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CabsOnline - Admin</title>
    <meta name="author" content="Michele Peghini - 101940042">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        // create current date and time use for query and to display in page.
        $current_date = date_create_from_format("d-m-Y",date("d-m-Y"));
        $current_time = date_create_from_format("H:i",date("H:i"));
        //create a 2 hour interval, to be added to $query_time 
        $query_interval = new DateInterval("PT2H");
        //modifies $query_time to be 2 hour after current time
        $query_time = date_create_from_format("H:i",date("H:i"));;
        $query_time->add($query_interval);
    ?>
    <nav id="main-navigation">
        <h1>CabsOnline</h1>
        <span> Admin page</span>
    </nav>
    <form id="admin-list-form">
        <legend>List bookings</legend>
        <fieldset>
            <p>Click the button to display all 'unassigned' bookings within the next 2 hours:</p>
            <input type="submit" name="list-all" value="List all">
            <span class="separator"> | </span>
            <span class="info"> Current date: <span class="info-content"><?php echo date_format($current_date, "d-m-Y")?></span> </span>
            <span class="separator"> | </span>
            <span class="info"> Current time: <span class="info-content"><?php echo date_format($current_time, "H:i")?></span> </span>
            <span class="separator"> | </span>
        </fieldset>
    </form>
    <?php
        require_once('utility.php');
        // if used has pressed list-all button
        if (isset($_GET['list-all']))
        {
            $list_bookings_query = "SELECT b.booking_number, c.customer_name, b.passenger_name, b.passenger_phone, b.pu_unit_no, b.pu_street_no, b.pu_street_name, b.pu_suburb, b.destination_suburb, b.pu_date, b.pu_time FROM booking b, customer c  WHERE "; 
            $list_bookings_query .= "b.booking_status = 'unassigned' AND "; // must be 'unassigned'
            $list_bookings_query .= "b.pu_date = '" . date_format($current_date, "Y-m-d") . "' AND "; // must be on the current day
            $list_bookings_query .= "b.pu_time <= '" . date_format($query_time, "H:i:s") . "' AND "; // must be less that 2 hours from now 
            $list_bookings_query .= "b.pu_time > '" . date_format($current_time, "H:i:s") . "' AND "; // ADDITIONAL, NOT REQUIRED must be after current time
            $list_bookings_query .= "c.email = b.email_address "; // customer email must match booking email
            $list_bookings_query .= "ORDER BY b.pu_time ASC;"; // ADDITIONAL, NOT REQUIRED order results by most urgent
        
            // connect to db. if connection error, call  print_msg() 
            $connect = @mysqli_connect($host, $db_user['name'], $db_user['pwd'], $db_name);
            if (!$connect)
            {
                 print_msg(mysqli_connect_error(),"error");
            }

            // query db
            $result = @mysqli_query($connect, $list_bookings_query);
            if (!$result)
            {
                 print_msg("Unable to fetch results from database!","error");
            }

            // if query has noresults, display error message
            if (mysqli_affected_rows($connect) == 0)
            {
                echo print_msg("No results to show!", "error", 0);
            }
            else
            {
                $result_table = "<div class='results'><table class='results-table'><tr><th>Ref. #</th><th>Customer name</th><th>Passenger name</th><th>Passenger phone</th><th>Pickup address</th><th>Destination</th><th>Pickup date and time</th></tr>";
                // fetch results as associative array 
                while ($row = mysqli_fetch_assoc($result))
                {   
                    $result_table .= "<tr>"; 
                    $result_table .= "<td>{$row['booking_number']}</td>";
                    $result_table .= "<td>{$row['customer_name']}</td>";
                    $result_table .= "<td>{$row['passenger_name']}</td>";
                    $result_table .= "<td>{$row['passenger_phone']}</td>";
                    // merge address into one string
                    $result_table .= "<td>";
                        //if pu_unit_no is not empty, add number and trailing / otherwise add ""
                    $result_table .= ($row['pu_unit_no'] != "") ? "{$row['pu_unit_no']}/" : "";
                    $result_table .= $row['pu_street_no'] . " " . $row['pu_street_name'] . ", " . $row['pu_suburb'];
                    $result_table .= "</td>";
    
                    $result_table .= "<td>{$row['destination_suburb']}</td>";
                    
                    //merge pu_date and pu_time niot one field
                    $result_table .= "<td>" . $row['pu_date'] . " " . $row['pu_time'] . "</td>";
                    $result_table .= "</tr>";
                }
                $result_table .= "</table></div>";
                echo $result_table;
                mysqli_free_results($connect);
            }
        }
    ?>
    <form id="admin-update-form">
        <legend>Update booking</legend>
        <fieldset>
            <p>Enter booking Reference Number in input field to update the booking status to 'assigned'.</p>
            <label for="booking-number">Reference number:</label>
            <input type="number" name="booking-number" id="booking-number" pattern="^[0-9]{1,}$" required>
            <input type="submit" value="Update Status">
        </fieldset>
    </form>
    <?php
        if (isset($_GET['booking-number']))
        {   
            // store booking-number in a variable and validate it
            $booking_number = trim($_GET['booking-number']);
            if (!validate($booking_number, 10, "/^[0-9]{1,}$/"))
            {
                 print_msg("Invalid booking number format!","error");
            }

            // build booking query
            $update_booking_query = "UPDATE booking SET booking_status = 'assigned' WHERE booking_number = '$booking_number';";
            // connect to db
            $connect = @mysqli_connect($host, $db_user['name'], $db_user['pwd'], $db_name);
            if(!$connect)
            {
                 print_msg("Unable to connect to db!","error");
            }
            // query db
            $result = mysqli_query($connect, $update_booking_query);
            // if query had no affected rows print erro message
            if(mysqli_affected_rows($connect) == 0)
            {
                 print_msg("No booking found for given reference number: $booking_number!","error");
            }

            // query successful, print success message and close connection
            print_msg("The booking request $booking_number has been properly assigned!", "success", 0);
            mysqli_close($connect);
        }
    ?>
</body>
</html>
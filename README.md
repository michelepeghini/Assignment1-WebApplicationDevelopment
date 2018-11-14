<h2>Description</h2>
<p>This is a simple website developed as an assignment for one of my units at university.</p>
<p>The purpose of the assignment is to perform simple DB interaction, managing session state (i.e. login), client-side and server-side data validation. </p>
<p>The pages use embedded php instead of XMLHttpRequests, this was a requirement of the assignment and beyond my choice.</p>

<p>The website allows to register as new user, to login into the system and to make a booking (after login), which will be stored in the DB.</p>
<p>The website has an admin page (with no login) that shows all 'unassigned' bookings in the DB and allows to process a specific booking by providing its Id.</p>
<p>Once processed the booking's status is merely changed to 'assigned' on the DB.</p>

<h2>List of Files</h2>
<ul>
<li>admin.php: page for admin functions</li>
<li>booking.php: page for booking a cab</li>
<li>cabsonline.sql: sql statements for creating the ‘booking’ and ‘customer’ tables</li>
<li>login.php: page for logging into the system</li>
<li>register.php: page for registering into the system</li>
<li>style.css: CSS file containing styles for all pages</li>
<li>utility.php: php file containing variables for db connection and utility functions shared across pages</li>
</ul>

<h2>Deployment</h2>
<ol>
    <li>Load the cabsonline.sql in phpMyAdmin, this will create the DB with no data.</li>
    <li>Copy files in your local 'www' directory.</li>
</ol>
NOTE: The php script 'utility.php' holds variables for db, host, user name and password. The current user is set to 'root' with no password.

<h2>Instructions</h2>
<ol>
    <li>Connect to register page (register.php).</li>
    <li>Fill in the form and click Register.</li>
    <li>If submitted data is validated successfully, the system will: 
        <ul>
            <li>Register your details into the DB; </li>
            <li>Log you into the system by starting a session on the server; </li>
            <li>Redirect you to the booking page</li>
        </ul>
    </li>
    <li>The booking page displays the email of user on the navbar, the Pickup Date and Pickup Time fields are pre-populated server-side.
        <ul>
            <li>Pickup Date is the current server date</li>
            <li>Pickup Time is the server time + 1 hour</li>
        </ul>
    NOTE: The server date and time is assumed to be the same of the user location. Server-side scripting does not take into account of user location and time zones. 
    </li>
    <li>Once the form is filled in and submitted, the system shows a ‘success’ notification and an email is sent to the currently logged in user account.</li>
    <li>If already registered, connect to the login page (login.php), successful login will take you to the booking page (booking.php).</li>
    <li>The admin page (admin.php) displays two forms. 
        <ul>
            <li>‘List bookings’ has a List All button that displays all bookings that are after current time and no more than 2 hours after current time. Current server date and time are also displayed, for the sake of reference.  After clicking List All button, the bookings are displayed.</li>
            <li>‘Update Bookings’ allows to enter a booking reference number and by clicking the Update Status button, the status of the relative booking will be updated to ‘assigned’, and a success message will be displayed.</li>
        </ul>
    </li>
</ol>

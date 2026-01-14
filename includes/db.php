<?php
// includes/db.php
// ===LOCALHOST
/*
$servername = "localhost";
$username = "root";
$password = "";
$database = "citizen_service_portal";

$conn = new mysqli($servername, $username, $password, $database);

*/

//==ONLINE SERVER


$servername = "localhost";
$username = "chipasha_egovernance";
$password = "RwaYnLc2kdj6As8ggXKQ"; 
$dbname = "chipasha_egovernance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


                     
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>



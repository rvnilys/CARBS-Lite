<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit();
}

if (isset($_GET["id"])) {
    $booking_id = $_GET["id"];
    mysqli_query($conn, "UPDATE bookings SET status='rejected' WHERE booking_id='$booking_id'");
}

header("Location: admin_requests.php");
exit();
?>
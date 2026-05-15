<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "club_leader") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if (isset($_GET["id"])) {
    $booking_id = $_GET["id"];

    $sql = "UPDATE bookings 
            SET status='cancelled'
            WHERE booking_id='$booking_id'
            AND user_id='$user_id'
            AND status IN ('pending', 'approved')";

    mysqli_query($conn, $sql);
}

header("Location: my_bookings.php");
exit();
?>
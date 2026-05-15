<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit();
}

if (isset($_GET["id"])) {
    $booking_id = $_GET["id"];

    $booking_query = "SELECT * FROM bookings WHERE booking_id='$booking_id'";
    $booking_result = mysqli_query($conn, $booking_query);
    $booking = mysqli_fetch_assoc($booking_result);

    if ($booking) {
        $resource_id = $booking["resource_id"];
        $event_date = $booking["event_date"];
        $start_time = $booking["start_time"];
        $end_time = $booking["end_time"];

        $check = "SELECT * FROM bookings 
                  WHERE booking_id != '$booking_id'
                  AND resource_id='$resource_id'
                  AND event_date='$event_date'
                  AND status='approved'
                  AND start_time < '$end_time'
                  AND end_time > '$start_time'";

        $check_result = mysqli_query($conn, $check);

        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>
                    alert('Cannot approve. This slot already has an approved booking.');
                    window.location.href='admin_requests.php';
                  </script>";
        } else {
            mysqli_query($conn, "UPDATE bookings SET status='approved' WHERE booking_id='$booking_id'");
            header("Location: admin_requests.php");
            exit();
        }
    }
}

header("Location: admin_requests.php");
exit();
?>
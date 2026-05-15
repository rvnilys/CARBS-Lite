<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "club_leader") {
    header("Location: index.php");
    exit();
}

$message = "";
$user_id = $_SESSION["user_id"];

if (!isset($_GET["id"])) {
    header("Location: my_bookings.php");
    exit();
}

$booking_id = $_GET["id"];

$booking_sql = "SELECT * FROM bookings 
                WHERE booking_id='$booking_id' 
                AND user_id='$user_id' 
                AND status='pending'";

$booking_result = mysqli_query($conn, $booking_sql);

if (mysqli_num_rows($booking_result) == 0) {
    echo "<script>
            alert('Only pending bookings can be edited.');
            window.location.href='my_bookings.php';
          </script>";
    exit();
}

$booking = mysqli_fetch_assoc($booking_result);
$resources = mysqli_query($conn, "SELECT * FROM resources WHERE status='active'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resource_id = $_POST["resource_id"];
    $event_name = $_POST["event_name"];
    $event_date = $_POST["event_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $purpose = $_POST["purpose"];

    if ($start_time >= $end_time) {
        $message = "<div class='error'>End time must be later than start time.</div>";
    } else {
        $check = "SELECT * FROM bookings 
                  WHERE booking_id != '$booking_id'
                  AND resource_id='$resource_id'
                  AND event_date='$event_date'
                  AND status IN ('pending', 'approved')
                  AND start_time < '$end_time'
                  AND end_time > '$start_time'";

        $check_result = mysqli_query($conn, $check);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "<div class='error'>This resource is unavailable for the selected time slot.</div>";
        } else {
            $update = "UPDATE bookings SET
                        resource_id='$resource_id',
                        event_name='$event_name',
                        event_date='$event_date',
                        start_time='$start_time',
                        end_time='$end_time',
                        purpose='$purpose'
                       WHERE booking_id='$booking_id'
                       AND user_id='$user_id'
                       AND status='pending'";

            if (mysqli_query($conn, $update)) {
                echo "<script>
                        alert('Booking updated successfully.');
                        window.location.href='my_bookings.php';
                      </script>";
                exit();
            } else {
                $message = "<div class='error'>Error updating booking.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking - CARBS Lite</title>
    <link rel="stylesheet" type="text/css" href="./style.css?v=80">
</head>
<body>

<div class="app-layout">

    <aside class="sidebar">
        <div class="logo-box">
            <h2>CARBS Lite</h2>
            <p>Club Resource Booking</p>
        </div>

        <nav class="sidebar-menu">
            <a href="club_dashboard.php"> Dashboard</a>
            <a href="availability.php">Check Availability</a>
            <a href="create_booking.php"> Create Booking</a>
            <a class="active" href="my_bookings.php"> My Bookings</a>
            <a href="logout.php"> Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div class="page-title">
                <h1>Edit Booking</h1>
                <p>You can only edit bookings that are still pending.</p>
            </div>

            <div class="profile-box">
                <div class="profile-avatar">CL</div>
                <div class="profile-info">
                    <h4><?php echo $_SESSION["name"]; ?></h4>
                    <p>Club Leader</p>
                </div>
            </div>
        </div>

        <div class="panel">
            <h2>Update Booking Details</h2>

            <?php echo $message; ?>

            <form method="POST">
                <label>Event Name</label>
                <input type="text" name="event_name" value="<?php echo $booking["event_name"]; ?>" required>

                <label>Resource</label>
                <select name="resource_id" required>
                    <?php while ($row = mysqli_fetch_assoc($resources)) { ?>
                        <option value="<?php echo $row["resource_id"]; ?>"
                            <?php if ($booking["resource_id"] == $row["resource_id"]) echo "selected"; ?>>
                            <?php echo $row["resource_name"]; ?>
                        </option>
                    <?php } ?>
                </select>

                <label>Event Date</label>
                <input type="date" name="event_date" value="<?php echo $booking["event_date"]; ?>" required>

                <label>Start Time</label>
                <input type="time" name="start_time" value="<?php echo substr($booking["start_time"], 0, 5); ?>" required>

                <label>End Time</label>
                <input type="time" name="end_time" value="<?php echo substr($booking["end_time"], 0, 5); ?>" required>

                <label>Purpose</label>
                <textarea name="purpose" rows="4" required><?php echo $booking["purpose"]; ?></textarea>

                <button type="submit">Update Booking</button>
            </form>
        </div>

    </main>

</div>

</body>
</html>
<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "club_leader") {
    header("Location: index.php");
    exit();
}

$message = "";
$user_id = $_SESSION["user_id"];

$prefill_resource = isset($_GET["resource_id"]) ? $_GET["resource_id"] : "";
$prefill_date = isset($_GET["event_date"]) ? $_GET["event_date"] : "";
$prefill_start = isset($_GET["start_time"]) ? $_GET["start_time"] : "";
$prefill_end = isset($_GET["end_time"]) ? $_GET["end_time"] : "";

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
                  WHERE resource_id='$resource_id'
                  AND event_date='$event_date'
                  AND status IN ('pending', 'approved')
                  AND start_time < '$end_time'
                  AND end_time > '$start_time'";

        $check_result = mysqli_query($conn, $check);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "<div class='error'>This resource is unavailable for the selected time slot.</div>";
        } else {
            $sql = "INSERT INTO bookings 
                    (user_id, resource_id, event_name, event_date, start_time, end_time, purpose, status)
                    VALUES 
                    ('$user_id', '$resource_id', '$event_name', '$event_date', '$start_time', '$end_time', '$purpose', 'pending')";

            if (mysqli_query($conn, $sql)) {
                $message = "<div class='success'>Booking request submitted successfully.</div>";
            } else {
                $message = "<div class='error'>Error creating booking.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Booking - CARBS Lite</title>
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
            <a href="availability.php"> Check Availability</a>
            <a class="active" href="create_booking.php">Create Booking</a>
            <a href="my_bookings.php"> My Bookings</a>
            <a href="logout.php"> Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div class="page-title">
                <h1>Create Booking</h1>
                <p>Submit a booking request for a shared campus resource.</p>
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
            <h2>Booking Details</h2>

            <?php echo $message; ?>

            <form method="POST">
                <label>Event Name</label>
                <input type="text" name="event_name" required>

                <label>Resource</label>
                <select name="resource_id" required>
                    <option value="">Select Resource</option>
                    <?php while ($row = mysqli_fetch_assoc($resources)) { ?>
                        <option value="<?php echo $row["resource_id"]; ?>"
                            <?php if ($prefill_resource == $row["resource_id"]) echo "selected"; ?>>
                            <?php echo $row["resource_name"]; ?>
                        </option>
                    <?php } ?>
                </select>

                <label>Event Date</label>
                <input type="date" name="event_date" value="<?php echo $prefill_date; ?>" required>

                <label>Start Time</label>
                <input type="time" name="start_time" value="<?php echo substr($prefill_start, 0, 5); ?>" required>

                <label>End Time</label>
                <input type="time" name="end_time" value="<?php echo substr($prefill_end, 0, 5); ?>" required>

                <label>Purpose</label>
                <textarea name="purpose" rows="4" required></textarea>

                <button type="submit">Submit Booking</button>
            </form>
        </div>

    </main>

</div>

</body>
</html>
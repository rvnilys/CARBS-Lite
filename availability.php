<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "club_leader") {
    header("Location: index.php");
    exit();
}

$resources = mysqli_query($conn, "SELECT * FROM resources WHERE status='active'");

$selected_resource = "";
$selected_date = "";

$slots = [
    ["09:00:00", "10:00:00"],
    ["10:00:00", "11:00:00"],
    ["11:00:00", "12:00:00"],
    ["12:00:00", "13:00:00"],
    ["13:00:00", "14:00:00"],
    ["14:00:00", "15:00:00"],
    ["15:00:00", "16:00:00"],
    ["16:00:00", "17:00:00"],
    ["17:00:00", "18:00:00"]
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_resource = $_POST["resource_id"];
    $selected_date = $_POST["event_date"];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Availability - CARBS Lite</title>
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
            <a class="active" href="availability.php"> Check Availability</a>
            <a href="create_booking.php"> Create Booking</a>
            <a href="my_bookings.php"> My Bookings</a>
            <a href="logout.php"> Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div class="page-title">
                <h1>Resource Availability</h1>
                <p>Select a resource and date to view available time slots.</p>
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
            <h2>Check Available Slots</h2>

            <form method="POST">
                <label>Resource</label>
                <select name="resource_id" required>
                    <option value="">Select Resource</option>
                    <?php while ($row = mysqli_fetch_assoc($resources)) { ?>
                        <option value="<?php echo $row["resource_id"]; ?>"
                            <?php if ($selected_resource == $row["resource_id"]) echo "selected"; ?>>
                            <?php echo $row["resource_name"]; ?>
                        </option>
                    <?php } ?>
                </select>

                <label>Date</label>
                <input type="date" name="event_date" value="<?php echo $selected_date; ?>" required>

                <button type="submit">Check Availability</button>
            </form>
        </div>

        <?php if ($selected_resource != "" && $selected_date != "") { ?>
            <div class="panel recent-panel">
                <h2>Available Time Slots</h2>

                <table>
                    <tr>
                        <th>Time Slot</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php foreach ($slots as $slot) { 
                        $start = $slot[0];
                        $end = $slot[1];

                        $check = "SELECT * FROM bookings 
                                  WHERE resource_id='$selected_resource'
                                  AND event_date='$selected_date'
                                  AND status IN ('pending', 'approved')
                                  AND start_time < '$end'
                                  AND end_time > '$start'";

                        $result = mysqli_query($conn, $check);

                        if (mysqli_num_rows($result) > 0) {
                            $status = "Booked";
                            $class = "status-rejected";
                            $action = "Not Available";
                        } else {
                            $status = "Available";
                            $class = "status-approved";
                            $action = "<a class='btn-small' href='create_booking.php?resource_id=$selected_resource&event_date=$selected_date&start_time=$start&end_time=$end'>Book</a>";
                        }
                    ?>
                        <tr>
                            <td><?php echo substr($start, 0, 5) . " - " . substr($end, 0, 5); ?></td>
                            <td><span class="<?php echo $class; ?>"><?php echo $status; ?></span></td>
                            <td><?php echo $action; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

    </main>

</div>

</body>
</html>
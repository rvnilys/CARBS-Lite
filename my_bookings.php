<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "club_leader") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT bookings.*, resources.resource_name 
        FROM bookings
        JOIN resources ON bookings.resource_id = resources.resource_id
        WHERE bookings.user_id='$user_id'
        ORDER BY bookings.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings - CARBS Lite</title>
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
            <a href="create_booking.php"> Create Booking</a>
            <a class="active" href="my_bookings.php">📋 My Bookings</a>
            <a href="logout.php"> Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div class="page-title">
                <h1>My Bookings</h1>
                <p>View, edit, or cancel your booking requests.</p>
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
            <h2>Booking History</h2>

            <table>
                <tr>
                    <th>Event</th>
                    <th>Resource</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row["event_name"]; ?></td>
                            <td><?php echo $row["resource_name"]; ?></td>
                            <td><?php echo $row["event_date"]; ?></td>
                            <td><?php echo substr($row["start_time"], 0, 5) . " - " . substr($row["end_time"], 0, 5); ?></td>
                            <td><?php echo $row["purpose"]; ?></td>
                            <td>
                                <span class="status-<?php echo $row["status"]; ?>">
                                    <?php echo ucfirst($row["status"]); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row["status"] == "pending") { ?>
                                    <a class="btn-small" href="edit_booking.php?id=<?php echo $row["booking_id"]; ?>">Edit</a>
                                    <a class="btn-small danger" href="cancel_booking.php?id=<?php echo $row["booking_id"]; ?>">Cancel</a>
                                <?php } elseif ($row["status"] == "approved") { ?>
                                    <a class="btn-small danger" href="cancel_booking.php?id=<?php echo $row["booking_id"]; ?>">Cancel</a>
                                <?php } else { ?>
                                    View Only
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7">No bookings found.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </main>

</div>

</body>
</html>
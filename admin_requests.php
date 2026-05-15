<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit();
}

$sql = "SELECT bookings.*, users.name, resources.resource_name 
        FROM bookings
        JOIN users ON bookings.user_id = users.user_id
        JOIN resources ON bookings.resource_id = resources.resource_id
        ORDER BY bookings.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Requests - CARBS Lite</title>
    <link rel="stylesheet" type="text/css" href="./style.css?v=80">
</head>
<body>

<div class="app-layout">

    <aside class="sidebar">
        <div class="logo-box">
            <h2>CARBS Lite</h2>
            <p>Student Affairs Panel</p>
        </div>

        <nav class="sidebar-menu">
            <a href="admin_dashboard.php"> Dashboard</a>
            <a class="active" href="admin_requests.php"> Booking Requests</a>
            <a href="logout.php"> Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div class="page-title">
                <h1>Booking Requests</h1>
                <p>Approve, reject, or cancel club resource booking requests.</p>
            </div>

            <div class="profile-box">
                <div class="profile-avatar">AD</div>
                <div class="profile-info">
                    <h4><?php echo $_SESSION["name"]; ?></h4>
                    <p>Student Affairs Admin</p>
                </div>
            </div>
        </div>

        <div class="panel">
            <h2>All Booking Requests</h2>

            <table>
                <tr>
                    <th>Club Leader</th>
                    <th>Event</th>
                    <th>Resource</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row["name"]; ?></td>
                            <td><?php echo $row["event_name"]; ?></td>
                            <td><?php echo $row["resource_name"]; ?></td>
                            <td><?php echo $row["event_date"]; ?></td>
                            <td><?php echo substr($row["start_time"], 0, 5) . " - " . substr($row["end_time"], 0, 5); ?></td>
                            <td>
                                <span class="status-<?php echo $row["status"]; ?>">
                                    <?php echo ucfirst($row["status"]); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row["status"] == "pending") { ?>
                                    <a class="btn-small" href="approve_booking.php?id=<?php echo $row["booking_id"]; ?>">Approve</a>
                                    <a class="btn-small danger" href="reject_booking.php?id=<?php echo $row["booking_id"]; ?>">Reject</a>
                                <?php } elseif ($row["status"] == "approved") { ?>
                                    <a class="btn-small danger" href="admin_cancel_booking.php?id=<?php echo $row["booking_id"]; ?>">Cancel</a>
                                <?php } else { ?>
                                    View Only
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7">No booking requests found.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </main>

</div>

</body>
</html>
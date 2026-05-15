<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "club_leader") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=$user_id"))["total"];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=$user_id AND status='pending'"))["total"];
$approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=$user_id AND status='approved'"))["total"];
$cancelled = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=$user_id AND status='cancelled'"))["total"];

$today = date("Y-m-d");

$today_sql = "SELECT bookings.*, resources.resource_name 
              FROM bookings 
              JOIN resources ON bookings.resource_id = resources.resource_id
              WHERE bookings.user_id=$user_id
              AND bookings.event_date='$today'
              ORDER BY bookings.start_time ASC";

$today_result = mysqli_query($conn, $today_sql);

$recent_sql = "SELECT bookings.*, resources.resource_name 
               FROM bookings 
               JOIN resources ON bookings.resource_id = resources.resource_id
               WHERE bookings.user_id=$user_id
               ORDER BY bookings.created_at DESC
               LIMIT 5";

$recent_result = mysqli_query($conn, $recent_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Club Dashboard - CARBS Lite</title>
    <link rel="stylesheet" type="text/css" href="./style.css?v=50">
</head>
<body>

<div class="app-layout">

    <aside class="sidebar">
        <div class="logo-box">
            <h2>CARBS Lite</h2>
            <p>Club Resource Booking</p>
        </div>

        <nav class="sidebar-menu">
    <a class="active" href="club_dashboard.php">Dashboard</a>
    <a href="availability.php"> Check Availability</a>
    <a href="create_booking.php">Create Booking</a>
    <a href="my_bookings.php">My Bookings</a>
    <a href="logout.php">Logout</a>
</nav>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div class="page-title">
                <h1>Club Leader Dashboard</h1>
                <p>Manage your club bookings and resource requests.</p>
            </div>

            <div class="profile-box">
                <div class="profile-avatar">CL</div>
                <div class="profile-info">
                    <h4><?php echo $_SESSION["name"]; ?></h4>
                    <p>Club Leader</p>
                </div>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <div class="card-icon">📅</div>
                <h3>Total Bookings</h3>
                <p><?php echo $total; ?></p>
                <span class="trend">All booking records</span>
            </div>

            <div class="card">
                <div class="card-icon">⏳</div>
                <h3>Pending</h3>
                <p><?php echo $pending; ?></p>
                <span class="trend">Waiting for admin review</span>
            </div>

            <div class="card">
                <div class="card-icon">✅</div>
                <h3>Approved</h3>
                <p><?php echo $approved; ?></p>
                <span class="trend">Confirmed bookings</span>
            </div>

            <div class="card">
                <div class="card-icon">❌</div>
                <h3>Cancelled</h3>
                <p><?php echo $cancelled; ?></p>
                <span class="trend">Cancelled requests</span>
            </div>
        </div>

        <div class="content-grid">
            <div class="panel">
                <h2>Quick Actions</h2>

                <div class="quick-actions">
                    <a class="quick-card" href="availability.php">
                        <span>🔎</span>
                        <strong>Check Availability</strong>
                        <small>View available time slots</small>
                    </a>

                    <a class="quick-card" href="create_booking.php">
                        <span>➕</span>
                        <strong>Create Booking</strong>
                        <small>Submit a new request</small>
                    </a>

                    <a class="quick-card" href="my_bookings.php">
                        <span>📋</span>
                        <strong>My Bookings</strong>
                        <small>Track booking status</small>
                    </a>
                </div>
            </div>

            <div class="panel">
                <h2>Today’s Schedule</h2>

                <table>
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>Status</th>
                    </tr>

                    <?php if (mysqli_num_rows($today_result) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($today_result)) { ?>
                            <tr>
                                <td><?php echo substr($row["start_time"], 0, 5); ?></td>
                                <td><?php echo $row["event_name"]; ?></td>
                                <td><span class="status-<?php echo $row["status"]; ?>"><?php echo ucfirst($row["status"]); ?></span></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3">No bookings scheduled for today.</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <div class="panel" style="margin-top: 24px;">
            <h2>Recent Activity</h2>

            <table>
                <tr>
                    <th>Event</th>
                    <th>Resource</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>

                <?php if (mysqli_num_rows($recent_result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($recent_result)) { ?>
                        <tr>
                            <td><?php echo $row["event_name"]; ?></td>
                            <td><?php echo $row["resource_name"]; ?></td>
                            <td><?php echo $row["event_date"]; ?></td>
                            <td><?php echo substr($row["start_time"], 0, 5) . " - " . substr($row["end_time"], 0, 5); ?></td>
                            <td><span class="status-<?php echo $row["status"]; ?>"><?php echo ucfirst($row["status"]); ?></span></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">No recent bookings found.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </main>
</div>

</body>
</html>
<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit();
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings"))["total"];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE status='pending'"))["total"];
$approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE status='approved'"))["total"];
$cancelled = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE status='cancelled'"))["total"];

$today = date("Y-m-d");

$today_sql = "SELECT bookings.*, users.name, resources.resource_name
              FROM bookings
              JOIN users ON bookings.user_id = users.user_id
              JOIN resources ON bookings.resource_id = resources.resource_id
              WHERE bookings.event_date='$today'
              ORDER BY bookings.start_time ASC";

$today_result = mysqli_query($conn, $today_sql);

$recent_sql = "SELECT bookings.*, users.name, resources.resource_name
               FROM bookings
               JOIN users ON bookings.user_id = users.user_id
               JOIN resources ON bookings.resource_id = resources.resource_id
               ORDER BY bookings.created_at DESC
               LIMIT 5";

$recent_result = mysqli_query($conn, $recent_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - CARBS Lite</title>
    <link rel="stylesheet" type="text/css" href="./style.css?v=70">
</head>
<body>

<div class="app-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo-box">
            <h2>CARBS Lite</h2>
            <p>Student Affairs Panel</p>
        </div>

        <nav class="sidebar-menu">
            <a class="active" href="admin_dashboard.php"> Dashboard</a>
            <a href="admin_requests.php"> Review Booking Requests</a>
            <a href="logout.php"> Logout</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOP BAR -->
        <div class="topbar">
            <div class="page-title">
                <h1>Admin Dashboard</h1>
                <p>Review booking requests and monitor campus resource usage.</p>
            </div>

            <div class="profile-box">
                <div class="profile-avatar">AD</div>
                <div class="profile-info">
                    <h4><?php echo $_SESSION["name"]; ?></h4>
                    <p>Student Affairs Admin</p>
                </div>
            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="cards">
            <div class="card">
                <div class="card-icon">📊</div>
                <h3>Total Bookings</h3>
                <p><?php echo $total; ?></p>
                <span class="trend">All requests received</span>
            </div>

            <div class="card">
                <div class="card-icon">⏳</div>
                <h3>Pending Requests</h3>
                <p><?php echo $pending; ?></p>
                <span class="trend">Needs admin action</span>
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
                <span class="trend">Cancelled bookings</span>
            </div>
        </div>

        <!-- QUICK ACTION + TODAY SCHEDULE -->
        <div class="content-grid">

            <div class="panel">
                <h2>Quick Action</h2>

                <div class="quick-actions">
                    <a class="quick-card" href="admin_requests.php">
                        <span>📥</span>
                        <strong>Review Booking Requests</strong>
                        <small>Approve, reject, or cancel bookings</small>
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
                                <td>
                                    <span class="status-<?php echo $row["status"]; ?>">
                                        <?php echo ucfirst($row["status"]); ?>
                                    </span>
                                </td>
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

        <!-- RECENT BOOKING ACTIVITY - INSIDE MAIN CONTENT -->
        <div class="panel recent-panel">
            <h2>Recent Booking Activity</h2>

            <table>
                <tr>
                    <th>Club Leader</th>
                    <th>Event</th>
                    <th>Resource</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>

                <?php if (mysqli_num_rows($recent_result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($recent_result)) { ?>
                        <tr>
                            <td><?php echo $row["name"]; ?></td>
                            <td><?php echo $row["event_name"]; ?></td>
                            <td><?php echo $row["resource_name"]; ?></td>
                            <td><?php echo $row["event_date"]; ?></td>
                            <td>
                                <span class="status-<?php echo $row["status"]; ?>">
                                    <?php echo ucfirst($row["status"]); ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5">No recent booking activity found.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </main>

</div>

</body>
</html>
<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] == "club_leader") {
            header("Location: club_dashboard.php");
            exit();
        } else {
            header("Location: admin_dashboard.php");
            exit();
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CARBS Lite Login</title>
    <link rel="stylesheet" type="text/css" href="./style.css?v=50">
</head>
<body>

<div class="login-container">
    <h1>CARBS Lite</h1>
    <p>Club Activity & Resource Booking System</p>

    <?php if ($error != "") { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <div class="demo-login">
        <p><b>Demo Login:</b></p>
        <p>Club Leader: leader@apu.edu.my / 12345</p>
        <p>Admin: admin@apu.edu.my / 12345</p>
    </div>
</div>

</body>
</html>
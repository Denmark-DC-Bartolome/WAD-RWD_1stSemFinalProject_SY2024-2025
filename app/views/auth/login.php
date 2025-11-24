<?php
session_start();
require_once __DIR__ . "/../../config/db.php";

$error = "";


// Handle login form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Validate form fields
    if (!$email || !$password) {
        $error = "Please enter both email and password.";
    } else {
        $pdo = getDB();
}

        /// Look for the user in the database
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);




    // // Verify password
    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = [
            "id" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"]
        ];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Invalid email or password.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/../../../../assets/css/style.css">
</head>

<body>
    <div class="container">
        <h2>Login to Student Portal</h2>
        <?php if (isset($_GET["registered"])) echo "<p style='color:green;'>Registration successful! Please login.</p>"; ?>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn" type="submit">Login</button>
        </form>
        <p>Don’t have an account? <a href="register.php">Register</a></p>
    </div>
</body>

</html>
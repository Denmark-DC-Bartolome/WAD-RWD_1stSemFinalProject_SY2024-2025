<?php
require_once __DIR__ . "/../../config/db.php";

$error="";
$success="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);


    //VALIDATEE INPUT
    if (!$name || !$email || !$password) {
        $error = "All fields are required.";
    }
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } 
    else {
        $pdo = getDB();





        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        // $stmt->bindValue(1, $email, PDO::PARAM_STR);
        // $result = $stmt->execute();

        $stmt->execute([$email]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);




        if ($existing) {
            $error = "Email is already registered.";




        } else {
            

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, ?)");


            $stmt->execute([
                $name,
                $email,
                $hashedPassword,
                date("Y-m-d H:i:s")

            ]);


                // Redirect to login page
            header("Location: login.php?registered=true");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="/../../../../assets/css/style.css">
</head>

<body>
    <div class="container">
        <h2>Create an Account</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn" type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>
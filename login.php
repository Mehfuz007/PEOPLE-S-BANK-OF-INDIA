<?php
session_start();
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["name"] = $name;
            header("Location: dashboard.php"); // Redirect after successful login
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No user found with this email!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(201, 201, 201);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .marquee-container {
            width: 100%;
            background: #fff3cd;
            border-bottom: 1px solid #ffc107;
            padding: 5px 0;
            position: absolute;
            top: 0;
            text-align: center;
        }
        marquee {
            font-size: 16px;
            color: #ff5733;
            font-weight: bold;
        }
        .container {
            width: 350px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 50px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            background: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn.update-btn {
            background: rgb(7, 121, 235);
        }
        .forgot-password {
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
        img.logo {
            width: 80px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <!-- Scrolling Loan Interest Rates -->
    <div class="marquee-container">
        <marquee behavior="scroll" direction="left">
            📢 Today's Interest Rates: Gold Loan - 7.5% | Education Loan - 2.3% | Home Loan - 6.9% | Crop Loan - 5.5%
        </marquee>
    </div>

    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        
        <form method="post">
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" class="btn">Login</button>
        </form>

        <!-- Forgot Password Link -->
        <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>

        <!-- Update Password Button -->
        <form action="update_password.php">
            <button type="submit" class="btn update-btn">Update Password</button>
        </form>

        <a href="register.php">Don't have an account? Register</a>  
    </div>

</body>
</html>

<?php
session_start();
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = trim($_POST["phone"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if phone number exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE phone = ?");
            $stmt->bind_param("ss", $hashed_password, $phone);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: login.php?message=Password updated successfully!"); // Redirect to login
                exit();
            } else {
                $error = "Failed to update password!";
            }
        } else {
            $error = "Phone number not found!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(201, 201, 201);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 350px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 15px;
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            background: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .back-btn {
            background: #6c757d;
            margin-top: 5px;
        }
        .back-btn:hover {
            background: #5a6268;
        }
        p {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <form method="post">
            <input type="text" name="phone" placeholder="Enter Phone Number" required>
            <input type="password" name="new_password" placeholder="Enter New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit" class="btn">Update Password</button>
        </form>
        <a href="login.php"><button class="btn back-btn">Back to Login</button></a>
    </div>
</body>
</html>

<?php
session_start();
include "config.php"; // Connect to database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Check if email exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: login.php?message=Password updated successfully!"); // Redirect to login
                exit();
            } else {
                $error = "Failed to update password!";
            }
        } else {
            $error = "Email not found!";
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
    <title>Update Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Update Password</h2>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit">Update Password</button>
        </form>
        <form action="login.php">
            <button type="submit">Back to Login</button>
        </form>
    </div>
</body>
</html>

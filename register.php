<?php
session_start();
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all fields are filled
    if (!empty($_POST["name"]) && !empty($_POST["phone"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
        $name = trim($_POST["name"]);
        $phone = trim($_POST["phone"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);

        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Function to generate a unique account number
        function generateAccountNumber($conn) {
            do {
                $account_number = "ACC" . rand(1000000000, 9999999999); // Example: ACC1234567890
                $query = "SELECT account_number FROM users WHERE account_number = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $account_number);
                $stmt->execute();
                $result = $stmt->get_result();
            } while ($result->num_rows > 0); // Ensure uniqueness

            return $account_number;
        }

        // Check if email already exists
        $check_email_sql = "SELECT email FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<p style='color: red;'>Error: Email already exists! Please use a different email.</p>";
        } else {
            // Generate a unique account number
            $account_number = generateAccountNumber($conn);

            // Insert user into database
            $sql = "INSERT INTO users (name, phone, email, password, account_number) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $name, $phone, $email, $hashed_password, $account_number);

            if ($stmt->execute()) {
                echo "<p style='color: green;'>Registration successful! Your account number is: " . $account_number . "</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
        }

        $stmt->close();
    } else {
        echo "<p style='color: red;'>Please fill in all fields!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css"> <!-- Link CSS -->
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="post" action="register.php">
            <input type="text" name="name" placeholder="Enter Full Name" required>
            <input type="text" name="phone" placeholder="Enter Phone Number" required>
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Register</button>
        </form>
        <a href="login.php">Already have an account? Login</a>
    </div>
</body>
</html>

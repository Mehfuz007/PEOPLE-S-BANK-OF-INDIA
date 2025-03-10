<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "config.php"; // Database connection

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, account_number, balance FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $account_number, $balance);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
        <p><strong>Account Number:</strong> <?php echo htmlspecialchars($account_number); ?></p>
        <p>Your current balance: <strong>₹<?php echo number_format($balance, 2); ?></strong></p>

        <div class="dashboard-buttons">
            <form action="transaction.php">
                <button type="submit" class="btn">View Transactions</button>
            </form>
            <form action="transfer.php">
                <button type="submit" class="btn">Transfer Money</button>
            </form>
            <form action="logout.php">
                <button type="submit" class="btn logout-btn">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

$loggedInUser = $_SESSION['user'];
$message = "";

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$loggedInUser]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    $updateStmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE email = ?");
    try {
        $updateStmt->execute([$name, $phone, $loggedInUser]);
        $message = "Profile updated successfully!";
        // Refresh user data
        $stmt->execute([$loggedInUser]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error updating profile: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="tel"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            margin: 10px 0;
            color: #4CAF50;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
        }
        .links a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Profile</h1>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email (cannot be changed)</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>

            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

            <button type="submit">Update Profile</button>
        </form>
        <div class="links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>

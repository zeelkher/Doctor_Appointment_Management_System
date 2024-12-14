<?php
session_start();
require 'db.php';

if (!isset($_SESSION['Patient_Id'])) {
    header('Location: home.php');
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $patientId = $_SESSION['Patient_Id'];

    $query = "SELECT password FROM patients WHERE Patient_Id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $patientId);
    $stmt->execute();
    $stmt->bind_result($dbPassword);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($oldPassword, $dbPassword)) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $updateQuery = "UPDATE patients SET password = ? WHERE Patient_Id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('si', $hashedPassword, $patientId);

            if ($updateStmt->execute()) {
                $success = "Password changed successfully!";
                session_destroy();
                header('Location: home.php');
                exit();
            } else {
                $error = "Error updating password.";
            }

            $updateStmt->close();
        } else {
            $error = "New password and confirm password do not match.";
        }
    } else {
        $error = "Old password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #71b7e6, #9b59b6);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    padding: 0 20px; 
}

.container {
    background-color: #fff;
    padding: 30px 50px; 
    border-radius: 10px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    max-width: 500px; 
    width: 100%;
}

.title {
    font-size: 26px;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    text-align: center;
    font-size: 14px;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

form {
    display: flex;
    flex-direction: column;
}

.input-box {
    margin-bottom: 20px;
}

.input-box p {
    margin: 0 0 5px;
    font-size: 14px;
    color: #555;
}

.input-box input {
    padding: 14px; 
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: calc(100% - 5px); 
    box-sizing: border-box; 
    transition: border-color 0.3s ease;
}

.input-box input:focus {
    outline: none;
    border-color: #71b7e6;
}

input[type="submit"] {
    background: linear-gradient(135deg, #71b7e6, #9b59b6);
    color: #fff;
    border: none;
    padding: 14px; 
    font-size: 16px;
    border-radius: 5px;
    width: 99%; 
    cursor: pointer;
    text-align: center;
    transition: background 0.3s ease;
    box-sizing: border-box; 
}

input[type="submit"]:hover {
    background: linear-gradient(135deg, #9b59b6, #71b7e6);
}

.pdash a {
    color: #71b7e6;
    text-decoration: none;
    font-size: 14px;
    margin-top: 10px;
    text-align: center;
    display: block;
}

.pdash a:hover {
    color: #9b59b6;
}

    </style>
</head>
<body>
    <div class="container">
        <h2 class="title">Change Password</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="cp.php">
            <div class="user-details">
                <div class="input-box">
                    <p>Old Password</p>
                    <input type="password" name="old_password" required>
                </div>

                <div class="input-box">
                    <p>New Password</p>
                    <input type="password" name="new_password" required>
                </div>

                <div class="input-box">
                    <p>Confirm New Password</p>
                    <input type="password" name="confirm_password" required>
                </div>

                <input type="submit" value="Change Password">
            </div>
        </form>

        <div class="pdash">
            <a href="pdash.php">Back to Profile</a>
        </div>
    </div>
</body>
</html>

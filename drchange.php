<?php
include_once('db.php');
session_start();

if (!isset($_SESSION['Doctor_Id'])) {
    header('Location: drlogin.php');
    exit();
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
            background: linear-gradient(135deg, #1abc9c, #3498db);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-top: 5px;
            color: #333;
        }

        .btn {
            background-color: #1abc9c;
            color: #fff;
            padding: 14px 30px;
            border-radius: 25px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #16a085;
        }

        .message {
            margin-bottom: 15px;
            color: red;
            font-size: 16px;
            font-weight: 600;
        }

        .back-link {
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            color: #3498db;
            margin-top: 10px;
            display: inline-block;
        }

        .back-link:hover {
            color: #1abc9c;
        }

        /* Mobile responsiveness */
        @media (max-width: 600px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }

            .form-group input {
                padding: 12px;
            }

            .btn {
                padding: 12px 28px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" action="drchange.php">
            <div class="form-group">
                <label for="current-password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" placeholder="Enter Current Password" required>
            </div>
            <div class="form-group">
                <label for="new-password">New Password:</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter New Password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
            </div>
            <input type="submit" name="change_password" class="btn" value="Change Password">
        </form>
        <a href="drdash.php" class="back-link">Back to Profile</a>
    </div>
</body>
</html>

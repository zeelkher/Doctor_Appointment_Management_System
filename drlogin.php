<?php
session_start();

if (isset($_SESSION['Doctor_Id'])) {
    header("Location: drdash.php"); 
    exit();
}

if (isset($_POST['login'])) {
    $doctor_id = $_POST['doctor_id'] ?? null;
    $Doctor_password = $_POST['Doctor_password'] ?? null;

    if (!empty($doctor_id) && !empty($Doctor_password)) {
        include_once('db.php');

        $sql = "SELECT Doctor_Id FROM doctor WHERE Doctor_Id = ? AND Doctor_password = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $doctor_id, $Doctor_password); // 'is' indicates integer for id and string for password
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['Doctor_Id'] = $doctor_id;

            header("Location: drdash.php");
            exit;
        } else {
            echo "<script>alert('Invalid Doctor ID or Password');</script>";
        }
    } else {
        echo "<script>alert('Please fill in both fields');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login</title>
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
        }
        .container {
            background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .drimg img {
            width: 120px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 90%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .btn {
            background-color: #1abc9c;
            color: #fff;
            padding: 12px 30px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="drimg">
            <img src="img/dr.jpeg" alt="Doctor Image">
        </div>
        <form method="POST" action="drlogin.php">
            <div class="form-group">
                <label for="doctor_id">Doctor ID:</label>
                <input type="text" id="doctor_id" name="doctor_id" placeholder="Enter Doctor ID" required>
            </div>
            <div class="form-group">
                <label for="Doctor_password">Password:</label>
                <input type="password" id="Doctor_password" name="Doctor_password" placeholder="Enter Password" required>
            </div>
            <input type="submit" name="login" class="btn" value="Login">
        </form>
    </div>
</body>
</html>

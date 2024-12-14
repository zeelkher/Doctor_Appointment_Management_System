<?php
include_once('db.php');
session_start();

if (isset($_SESSION['Patient_Id'])) {
    header("Location: pdash.php");
    exit();
}

// Check if the user has a 'remember me' cookie
if (isset($_COOKIE['remember_me'])) {
    $cookie_data = $_COOKIE['remember_me'];
    list($Patient_Id, $token) = explode(':', $cookie_data);

    // Validate the token
    $query = "SELECT Patient_Id FROM patients WHERE Patient_Id = ? AND remember_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $Patient_Id, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, log in the user
        $_SESSION['Patient_Id'] = $Patient_Id;
        header("Location: pdash.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Patient_Email = htmlspecialchars($_POST['Patient_Email'] ?? '');
    $Patient_password = $_POST['Patient_password'] ?? '';
    $remember_me = isset($_POST['remember_me']) ? true : false;

    if (!empty($Patient_Email) && !empty($Patient_password)) {
        $query = "SELECT Patient_Id, password FROM patients WHERE Patient_Email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $Patient_Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $patient = $result->fetch_assoc();

            if (password_verify($Patient_password, $patient['password'])) {
                // Set session
                $_SESSION['Patient_Id'] = $patient['Patient_Id'];

                // Set remember me cookie if checked
                if ($remember_me) {
                    $token = bin2hex(random_bytes(16)); // Generate a random token
                    $cookie_data = $patient['Patient_Id'] . ':' . $token;

                    // Set the cookie to expire in 30 days
                    setcookie('remember_me', $cookie_data, time() + (30 * 24 * 60 * 60), "/", "", false, true);

                    // Store the token in the database
                    $update_query = "UPDATE patients SET remember_token = ? WHERE Patient_Id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("si", $token, $patient['Patient_Id']);
                    $update_stmt->execute();
                }

                header("Location: pdash.php"); // Redirect to patient dashboard
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "No account found with this email!";
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <title>Login</title>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-container h1 {
            font-size: 26px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }

        .login-container input {
            padding: 12px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            transition: border-color 0.3s ease;
        }

        .login-container input:focus {
            outline: none;
            border-color: #71b7e6;
        }

        .login-container .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-button {
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            color: #fff;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: background 0.3s ease;
        }

        .login-button:hover {
            background: linear-gradient(135deg, #9b59b6, #71b7e6);
        }

        .login-container a {
            color: #71b7e6;
            text-decoration: none;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
            display: block;
        }

        .login-container a:hover {
            color: #9b59b6;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h1>Login</h1>
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <label for="Patient_Email">Email:</label>
        <input type="email" id="Patient_Email" name="Patient_Email" required>

        <label for="Patient_password">Password:</label>
        <input type="password" id="Patient_password" name="Patient_password" required>

        <label for="remember_me">
            <input type="checkbox" id="remember_me" name="remember_me"> Remember Me
        </label>

        <button type="submit" class="login-button">Login</button>
    </form>
        <a href="fp.php">Forgot Password?</a>
        <a href="registration.php">Don't have an account? Register here</a>
</div>
</body>
</html>

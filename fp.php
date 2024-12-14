<?php
include_once('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Patient_Email = htmlspecialchars($_POST['Patient_Email'] ?? '');
    $security_answer = htmlspecialchars($_POST['security_answer'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($Patient_Email) && !empty($security_answer) && !empty($new_password)) {
        $query = "SELECT security_question, security_answer FROM patients WHERE Patient_Email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $Patient_Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $patient = $result->fetch_assoc();

            // Verify security answer
            if (password_verify($security_answer, $patient['security_answer'])) {
                // Update the password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE patients SET password = ? WHERE Patient_Email = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ss", $hashed_password, $Patient_Email);
                if ($update_stmt->execute()) {
                    echo "<script>alert('Password updated successfully!'); window.location.href='login.php';</script>";
                } else {
                    echo "<script>alert('Failed to update password!');</script>";
                }
                $update_stmt->close();
            } else {
                echo "<script>alert('Incorrect security answer!');</script>";
            }
        } else {
            echo "<script>alert('No account found with this email!');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all fields!');</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <form method="POST">
        <label for="Patient_Email">Email:</label>
        <input type="email" id="Patient_Email" name="Patient_Email" required>

        <label for="security_answer">Security Answer:</label>
        <input type="text" id="security_answer" name="security_answer" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>

        <button type="submit">Reset Password</button>
    </form>
</body>
</html>

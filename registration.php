<?php

include_once 'db.php';

$Patient_FirstName = $Patient_MiddleName = $Patient_LastName = $Patient_Email = $Patient_Phone = '';
$Patient_password = $Confirm_password = $Patient_DOB = $Patient_Marital_Status = $Patient_Gender = $Patient_Address = '';
$security_question = $security_answer = '';
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Patient_FirstName = htmlspecialchars($_POST['Patient_FirstName'] ?? '');
    $Patient_MiddleName = htmlspecialchars($_POST['Patient_MiddleName'] ?? '');
    $Patient_LastName = htmlspecialchars($_POST['Patient_LastName'] ?? '');
    $Patient_Email = htmlspecialchars($_POST['Patient_Email'] ?? '');
    $Patient_Phone = htmlspecialchars($_POST['Patient_Phone'] ?? '');
    $Patient_password = $_POST['Patient_password'] ?? '';
    $Confirm_password = $_POST['Confirm_password'] ?? '';
    $Patient_DOB = htmlspecialchars($_POST['Patient_DOB'] ?? '');
    $Patient_Marital_Status = $_POST['Patient_Marital_Status'] ?? '';
    $Patient_Gender = $_POST['Patient_Gender'] ?? '';
    $Patient_Address = htmlspecialchars($_POST['Patient_Address'] ?? '');
    $security_question = htmlspecialchars($_POST['Security_Question'] ?? '');
    $security_answer = htmlspecialchars($_POST['Security_Answer'] ?? '');

    // Validate inputs
    if (empty($security_question)) {
        $errors['Security_Question'] = "Security question is required.";
    }
    if (empty($security_answer)) {
        $errors['Security_Answer'] = "Security answer is required.";
    }

    // Check if email is already registered
    $email_check_stmt = $conn->prepare("SELECT Patient_Email FROM patients WHERE Patient_Email = ?");
    $email_check_stmt->bind_param("s", $Patient_Email);
    $email_check_stmt->execute();
    $email_check_stmt->store_result();

    if ($email_check_stmt->num_rows > 0) {
        $errors['Patient_Email'] = "This email is already registered.";
    } else {
        if (empty($errors)) {
            $hashed_password = password_hash($Patient_password, PASSWORD_DEFAULT);
            $verified = 0;
            $profile_img = 'dp/default.png';

            $stmt = $conn->prepare(
                "INSERT INTO patients (Patient_FirstName, Patient_MiddleName, Patient_LastName, Patient_Email, Patient_Phone, password, Patient_DOB, Patient_Marital_Status, Patient_Gender, Patient_Address, verified, profile_img, Security_Question, Security_Answer) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if ($stmt) {
                $stmt->bind_param(
                    "ssssssssssisss",
                    $Patient_FirstName,
                    $Patient_MiddleName,
                    $Patient_LastName,
                    $Patient_Email,
                    $Patient_Phone,
                    $hashed_password,
                    $Patient_DOB,
                    $Patient_Marital_Status,
                    $Patient_Gender,
                    $Patient_Address,
                    $verified,
                    $profile_img,
                    $security_question,
                    $security_answer
                );

                if ($stmt->execute()) {
                    $success_message = "Registration successful!";
                    header("Location: home.php");
                    exit(); 
                } else {
                    $errors['db'] = "Database error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $errors['db'] = "Error preparing the statement: " . $conn->error;
            }
        }
    }

    $email_check_stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <title>Registration Form</title>
    <style>
       body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: 100vh; /* Full viewport height */
            flex-direction: column; /* Ensure proper alignment for single item in center */
        }

        .container {
            width: 95%;
            max-width: 900px; /* Maximum width of the form */
            background: #fff;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            height: auto; /* Allow the form to expand based on its content */
            display: flex;
            flex-direction: column; /* Ensure content is stacked vertically */
            justify-content: flex-start; /* Align content to the top */
            padding-left: 40px; /* Increase left padding */
            padding-right: 40px; /* Increase right padding */
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 15px; /* Reduced gap for compactness */
            margin-bottom: 10px; /* Reduced margin for smaller spacing */
        }

        .container h1 {
            text-align: center;
            margin-bottom: 15px;
            color: #5bc0de;
            font-size: 24px;
        }

        .input-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .input-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #5bc0de;
        }

        .input-group input,
        .input-group select,
        .input-group textarea {
            padding: 8px; /* Reduced padding for smaller inputs */
            font-size: 14px; /* Smaller font size */
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            color: #333;
            transition: border-color 0.3s ease;
        }

        .input-group input:focus,
        .input-group select:focus,
        .input-group textarea:focus {
            border-color: #5bc0de;
        }

        .input-group textarea {
            resize: none;
            height: 80px; /* Smaller height for textarea */
        }

        .button {
            text-align: center;
        }

        .button input {
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            color: #fff;
            border: none;
            padding: 10px 20px; /* Reduced button padding */
            font-size: 16px; /* Slightly smaller button text */
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.9s ease;
            width: 100%; /* Smaller button width */
        }

        .button input:hover {
            background: linear-gradient(135deg, #9b59b6, #71b7e6);
        }

        .error {
            color: red;
            font-size: 12px; /* Smaller error text */
            margin-top: 5px;
        }

        .success {
            color: green;
            font-size: 12px; /* Smaller success text */
            margin-top: 5px;
        }

        input[type="submit"] {
            margin-top: 15px; /* Reduced margin for the submit button */
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px; /* Adjust padding for mobile */
            }

            .row {
                flex-direction: column;
                gap: 10px;
            }

            .input-group {
                flex: 1 1 100%;
            }
        }

        

    </style>
</head>
<body>
<div class="container">
    <h1>Registration Form</h1>
    <?php if (!empty($success_message)): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['db'])): ?>
        <div class="error"><?php echo $errors['db']; ?></div>
    <?php endif; ?>
    <form action="" method="post">
        <div class="row">
            <div class="input-group">
                <label for="Patient_FirstName">First Name</label>
                <input type="text" name="Patient_FirstName" value="<?php echo htmlspecialchars($Patient_FirstName); ?>">
                <?php if (!empty($errors['Patient_FirstName'])): ?>
                    <div class="error"><?php echo $errors['Patient_FirstName']; ?></div>
                <?php endif; ?>
            </div>
            <div class="input-group">
                <label for="Patient_MiddleName">Middle Name</label>
                <input type="text" name="Patient_MiddleName" value="<?php echo htmlspecialchars($Patient_MiddleName); ?>">
            </div>
            <div class="input-group">
                <label for="Patient_LastName">Last Name</label>
                <input type="text" name="Patient_LastName" value="<?php echo htmlspecialchars($Patient_LastName); ?>">
                <?php if (!empty($errors['Patient_LastName'])): ?>
                    <div class="error"><?php echo $errors['Patient_LastName']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="Patient_Email">Email</label>
                <input type="email" name="Patient_Email" value="<?php echo htmlspecialchars($Patient_Email); ?>">
                <?php if (!empty($errors['Patient_Email'])): ?>
                    <div class="error"><?php echo $errors['Patient_Email']; ?></div>
                <?php endif; ?>
            </div>
            <div class="input-group">
                <label for="Patient_Phone">Phone</label>
                <input type="text" name="Patient_Phone" value="<?php echo htmlspecialchars($Patient_Phone); ?>">
                <?php if (!empty($errors['Patient_Phone'])): ?>
                    <div class="error"><?php echo $errors['Patient_Phone']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="Patient_password">Password</label>
                <input type="password" name="Patient_password">
                <?php if (!empty($errors['Patient_password'])): ?>
                    <div class="error"><?php echo $errors['Patient_password']; ?></div>
                <?php endif; ?>
            </div>
            <div class="input-group">
                <label for="Confirm_password">Confirm Password</label>
                <input type="password" name="Confirm_password">
                <?php if (!empty($errors['Confirm_password'])): ?>
                    <div class="error"><?php echo $errors['Confirm_password']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="Patient_DOB">Date of Birth</label>
                <input type="date" name="Patient_DOB" placeholder="dd-mm-yyyy" value="<?php echo htmlspecialchars($Patient_DOB); ?>">
            </div>
            <div class="input-group">
                <label for="Patient_Marital_Status">Marital Status</label>
                <select name="Patient_Marital_Status">
                    <option value="Single" <?php echo $Patient_Marital_Status == 'Single' ? 'selected' : ''; ?>>Single</option>
                    <option value="Married" <?php echo $Patient_Marital_Status == 'Married' ? 'selected' : ''; ?>>Married</option>
                </select>
            </div>
            <div class="input-group">
                <label for="Patient_Gender">Gender</label>
                <select name="Patient_Gender">
                    <option value="Male" <?php echo $Patient_Gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $Patient_Gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo $Patient_Gender == 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="input-group">
                <label for="Patient_Address">Address</label>
                <textarea name="Patient_Address"><?php echo htmlspecialchars($Patient_Address); ?></textarea>
                <?php if (!empty($errors['Patient_Address'])): ?>
                    <div class="error"><?php echo $errors['Patient_Address']; ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row security-fields">
    <div class="input-group security-question">
        <label for="Security_Question">Security Question</label>
        <select name="Security_Question" required>
            <option value="" disabled selected>Select a question</option>
            <option value="What is your mother's maiden name?" <?php echo ($security_question == "What is your mother's maiden name?") ? 'selected' : ''; ?>>What is your mother's maiden name?</option>
            <option value="What is the name of your first pet?" <?php echo ($security_question == "What is the name of your first pet?") ? 'selected' : ''; ?>>What is the name of your first pet?</option>
            <option value="What was your first car?" <?php echo ($security_question == "What was your first car?") ? 'selected' : ''; ?>>What was your first car?</option>
            <option value="What was the name of your elementary school?" <?php echo ($security_question == "What was the name of your elementary school?") ? 'selected' : ''; ?>>What was the name of your elementary school?</option>
        </select>
        <?php if (!empty($errors['Security_Question'])): ?>
            <div class="error"><?php echo $errors['Security_Question']; ?></div>
        <?php endif; ?>
    </div>

    <div class="input-group security-answer">
        <label for="Security_Answer">Security Answer</label>
        <input type="text" name="Security_Answer" value="<?php echo htmlspecialchars($security_answer); ?>" required>
        <?php if (!empty($errors['Security_Answer'])): ?>
            <div class="error"><?php echo $errors['Security_Answer']; ?></div>
        <?php endif; ?>
    </div>
    </div>
        <div class="button">
            <input type="submit" value="Register">
        </div>

    <div class="forgot-password-link" style="text-align: center; margin-top: 10px;">
        <a href="fp.php" style="text-decoration: none; color: #5bc0de;">Forgot Password?</a>
    </div>

    </form>
</div>
</body>
</html>

<?php
include_once 'db.php';



$errors = [];
$patientDetails = [];

if (isset($_GET['Patient_Id'])) {
    $patientId = (int)$_GET['Patient_Id'];
    
    $stmt = $conn->prepare("SELECT * FROM patients WHERE Patient_Id = ?");
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $patientDetails = $result->fetch_assoc();
    } else {
        $errors[] = "Patient not found.";
    }
}

$patientDetails = array_merge([
    'Patient_FirstName' => '',
    'Patient_MiddleName' => '',
    'Patient_LastName' => '',
    'Patient_Maritial_Status' => '',
    'Patient_DOB' => '',
    'Patient_Gender' => '',
    'Patient_Address' => '',
    'Patient_Phone' => '',
    'Patient_Email' => '',
    'profile_img' => 'dp/default.png' 
], $patientDetails);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $maritalStatus = $_POST['maritalStatus'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    
    if (empty($firstName) || empty($lastName) || empty($maritalStatus) || empty($dob) || empty($gender) || empty($address) || empty($phone) || empty($email)) {
        $errors[] = "Please fill in all required fields.";
    }

    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "dp/";
        $imageFileType = strtolower(pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION));
        $target_file = $target_dir . $patientId . "." . $imageFileType;
        $uploadOk = 1;

        $check = getimagesize($_FILES['profile_img']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $errors[] = "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES['profile_img']['size'] > 5000000) {
            $errors[] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if (file_exists($target_file)) {
            unlink($target_file); 
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_file)) {
                
                $sql = "UPDATE patients SET profile_img = ? WHERE Patient_Id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('si', $target_file, $patientId);
                if (!$stmt->execute()) {
                    $errors[] = "Error saving the image path to the database.";
                }
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    }

    if (empty($errors)) {
        $updateStmt = $conn->prepare("UPDATE patients SET 
            Patient_FirstName = ?, 
            Patient_MiddleName = ?, 
            Patient_LastName = ?, 
            Patient_Marital_Status = ?, 
            Patient_DOB = ?, 
            Patient_Gender = ?, 
            Patient_Address = ?, 
            Patient_Phone = ?, 
            Patient_Email = ? 
            WHERE Patient_Id = ?");
        
        if (!$updateStmt) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $updateStmt->bind_param("sssssssssi", 
            $firstName, 
            $middleName, 
            $lastName, 
            $maritalStatus, 
            $dob, 
            $gender, 
            $address, 
            $phone, 
            $email, 
            $patientId
        );

        if ($updateStmt->execute()) {
            header("Location: pdash.php"); 
            exit();
        } else {
            $errors[] = "Error updating patient details: " . $updateStmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #1ABC9C;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            margin: 20px auto;
            padding: 40px;
            width: 80%;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .edit-form h2 {
            text-align: center;
            color: #1ABC9C;
            margin-bottom: 20px;
        }
        .edit-form label {
            font-size: 14px;
            margin-bottom: 6px;
            display: block;
        }
        .edit-form input,
        .edit-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .save-button {
            background-color: #1ABC9C;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .save-button:hover {
            background-color: #1ABC9D;
        }
        .error {
            color: red;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .error ul {
            list-style-type: none;
            padding: 0;
        }
        .error li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<header>
    <h1>Edit Profile</h1>
</header>

<div class="content">
    <form class="edit-form" method="POST" action="" enctype="multipart/form-data">
        <h2>Edit Profile</h2>

        <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($patientDetails['Patient_FirstName']) ?>" required>

        <label for="middleName">Middle Name:</label>
        <input type="text" id="middleName" name="middleName" value="<?= isset($patientDetails['Patient_MiddleName']) ? htmlspecialchars($patientDetails['Patient_MiddleName']) : '' ?>">

        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($patientDetails['Patient_LastName']) ?>" required>

        <label for="maritalStatus">Marital Status:</label>
        <select id="maritalStatus" name="maritalStatus" required>
            <option value="Single" <?= $patientDetails['Patient_Maritial_Status'] == 'Single' ? 'selected' : '' ?>>Single</option>
            <option value="Married" <?= $patientDetails['Patient_Maritial_Status'] == 'Married' ? 'selected' : '' ?>>Married</option>
            <option value="Divorced" <?= $patientDetails['Patient_Maritial_Status'] == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
        </select>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($patientDetails['Patient_DOB']) ?>" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male" <?= $patientDetails['Patient_Gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $patientDetails['Patient_Gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= $patientDetails['Patient_Gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($patientDetails['Patient_Address']) ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($patientDetails['Patient_Phone']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($patientDetails['Patient_Email']) ?>" required>

        <label for="profile_img">Profile Image:</label>
        <input type="file" id="profile_img" name="profile_img">

        <button type="submit" class="save-button">Save Changes</button>
    </form>
</div>

</body>
</html>

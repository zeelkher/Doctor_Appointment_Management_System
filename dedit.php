<?php
session_start(); 
include_once("db.php");


$doctorId = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : (isset($_SESSION['Doctor_Id']) ? $_SESSION['Doctor_Id'] : 0);


if ($doctorId === 0) {
    header('Location: home.php'); 
    exit;
}


$doctorDetailsQuery = "SELECT * FROM doctor WHERE Doctor_Id = $doctorId";
$resultDoctorDetails = mysqli_query($conn, $doctorDetailsQuery);
$doctorDetails = mysqli_fetch_assoc($resultDoctorDetails);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $firstName = $_POST['Doctor_FirstName'] ?: $doctorDetails['Doctor_FirstName'];
    $lastName = $_POST['Doctor_LastName'] ?: $doctorDetails['Doctor_LastName'];
    $address = $_POST['Doctor_Address'] ?: $doctorDetails['Doctor_Address'];
    $specialties = $_POST['Doctor_specialities'] ?: $doctorDetails['Doctor_specialities'];
    $phone = $_POST['Doctor_Phone'] ?: $doctorDetails['Doctor_Phone'];
    $email = $_POST['Doctor_Email'] ?: $doctorDetails['Doctor_Email'];
    $dob = $_POST['Doctor_DOB'] ?: $doctorDetails['Doctor_DOB'];

   
    $updateQuery = "UPDATE doctor 
                    SET Doctor_FirstName = '$firstName', Doctor_LastName = '$lastName', Doctor_Address = '$address', 
                        Doctor_specialities = '$specialties', Doctor_Phone = '$phone', Doctor_Email = '$email', Doctor_DOB = '$dob'
                    WHERE Doctor_Id = $doctorId";
    
    if (mysqli_query($conn, $updateQuery)) {
        header('Location: drdash.php'); // Redirect to dashboard after update
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor Details</title>
    <style>
        body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.form-container {
    background-color: #fff;
    max-width: 600px;
    margin: 50px auto;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #2C3E50;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 16px;
    color: #333;
    margin-bottom: 8px;
}

.form-group input {
    width: 100%;
    padding: 12px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

.form-group input:focus {
    border-color: #1abc9c;
    outline: none;
}

.submit-btn {
    width: 100%;
    padding: 14px;
    font-size: 16px;
    background-color: #1abc9c;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.submit-btn:hover {
    background-color: #16a085;
}

p {
    text-align: center;
    font-size: 14px;
    color: #7f8c8d;
}

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Doctor Details</h2>
        <form method="POST">
            <div class="form-group">
                <label for="Doctor_FirstName">First Name:</label>
                <input type="text" name="Doctor_FirstName" value="<?= htmlspecialchars($doctorDetails['Doctor_FirstName']) ?>">
            </div>
            <div class="form-group">
                <label for="Doctor_LastName">Last Name:</label>
                <input type="text" name="Doctor_LastName" value="<?= htmlspecialchars($doctorDetails['Doctor_LastName']) ?>">
            </div>
            <div class="form-group">
                <label for="Doctor_Address">Address:</label>
                <input type="text" name="Doctor_Address" value="<?= htmlspecialchars($doctorDetails['Doctor_Address']) ?>">
            </div>
            <div class="form-group">
                <label for="Doctor_specialities">Specialties:</label>
                <input type="text" name="Doctor_specialities" value="<?= htmlspecialchars($doctorDetails['Doctor_specialities']) ?>">
            </div>
            <div class="form-group">
                <label for="Doctor_Phone">Contact Number:</label>
                <input type="text" name="Doctor_Phone" value="<?= htmlspecialchars($doctorDetails['Doctor_Phone']) ?>">
            </div>
            <div class="form-group">
                <label for="Doctor_Email">Email:</label>
                <input type="email" name="Doctor_Email" value="<?= htmlspecialchars($doctorDetails['Doctor_Email']) ?>">
            </div>
            <div class="form-group">
                <label for="Doctor_DOB">Date of Birth (dd-mm-yyyy):</label>
                <input type="date" name="Doctor_DOB" value="<?= htmlspecialchars($doctorDetails['Doctor_DOB']) ?>">
            </div>
            <div class="form-group">
            <p style="color: gray;"> Leave the fields unchanged if you do not wish to update.</p>
            </div>
            
            <button type="submit" class="submit-btn">Save Changes</button>
        </form>
    </div>
</body>
</html>

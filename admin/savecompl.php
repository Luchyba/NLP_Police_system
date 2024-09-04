<?php
require_once('database/Database.php');
$db = new Database(); 

if (session_status() == PHP_SESSION_NONE) {
    include('session.php');
}

// Initialize response array
$response = array('error' => false, 'message' => '', 'url' => '');

// Collect form data
$trans_id = isset($_POST['uid']) ? $_POST['uid'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$tel = isset($_POST['tel']) ? $_POST['tel'] : '';
$occ = isset($_POST['occ']) ? $_POST['occ'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$age = isset($_POST['age']) ? $_POST['age'] : '';
$addrs = isset($_POST['addrs']) ? $_POST['addrs'] : '';
$region = isset($_POST['region']) ? $_POST['region'] : '';
$district = isset($_POST['district']) ? $_POST['district'] : '';
$loc = isset($_POST['loc']) ? $_POST['loc'] : '';
$crime_type = isset($_POST['crime_type']) ? $_POST['crime_type'] : '';
$id_type = isset($_POST['id_type']) ? $_POST['id_type'] : '';
$id_number = isset($_POST['id_number']) ? $_POST['id_number'] : '';
$id_image = isset($_POST['id_image']) ? $_POST['id_image'] : '';

// Session to hold the transaction ID
$_SESSION['trans_id'] = $trans_id;

// Validate required fields
$errors = array();
if (empty($name)) $errors[] = "The name cannot be empty.";
if (empty($gender)) $errors[] = "The gender field cannot be empty.";
if (empty($tel)) $errors[] = "The telephone number cannot be empty.";
if (empty($occ)) $errors[] = "The occupation field cannot be empty.";
if (empty($region)) $errors[] = "The region field cannot be empty.";
if (empty($district)) $errors[] = "The district field cannot be empty.";
if (empty($loc)) $errors[] = "The location field cannot be empty.";
if (empty($crime_type)) $errors[] = "The crime type field cannot be empty.";
if (empty($addrs)) $errors[] = "The address field cannot be empty.";
if (empty($age)) $errors[] = "The age field cannot be empty.";
if (empty($id_type)) $errors[] = "The ID type field cannot be empty.";
if (empty($id_number)) $errors[] = "The ID number field cannot be empty.";

// Handle ID Card Image
if (!empty($id_image)) {
    $imageParts = explode(";base64,", $id_image);
    $imageTypeAux = explode("image/", $imageParts[0]);
    $imageType = $imageTypeAux[1];
    $imageBase64 = base64_decode($imageParts[1]);

    // Define file path and name
    $filePath = "uploads/" . uniqid() . "." . $imageType;

    // Save the image file
    if (!file_put_contents($filePath, $imageBase64)) {
        $errors[] = "Failed to save ID card image.";
    }
} else {
    $errors[] = "No ID card image captured.";
}

// If there are errors, return them
if (!empty($errors)) {
    $response['error'] = true;
    $response['message'] = implode(" ", $errors);
} else {  
    $sql = "INSERT INTO complainant (case_id, comp_name, tel, region, district, loc, addrs, age, occupation, gender, id_type, id_number, id_image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [$trans_id, $name, $tel, $region, $district, $loc, $addrs, $age, $occ, $gender, $id_type, $id_number, $filePath];
    $success = $db->insertRow($sql, $params);

    if ($success) {
        $response['url'] = "addcase.php?id=staff&caseid=$trans_id&crimetype=$crime_type";
        $response['message'] = "Data saved successfully!";
    } else {
        $response['error'] = true;
        $response['message'] = "Failed to save data.";
    }
}

// Output JSON response
echo json_encode($response);

// Close database connection
$db->Disconnect();
?>

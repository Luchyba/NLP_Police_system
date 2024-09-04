<?php
require_once('database/Database.php');
$db = new Database(); 

if(session_status() == PHP_SESSION_NONE) {
    include('session.php');
    include('dbconnect.php');
    date_default_timezone_set("Africa/Accra"); 
    $date = date("Y-m-d H:i:s");
}

// Array to hold the error messages
$errors = array();

// Array to hold the JSON encoded data
$output = array('error' => false);

// Variables to hold the form data
$statement = ''; 
$case_id = ''; 
$staffid = $session_id; 
$crime = '';
$audio_data = null;
$audio_type = null;
$audio_name = null;

// Check if statement is empty
if(empty($_POST['statement'])){
    array_push($errors, "The field cannot be empty, ensure it is entered");
} else { 
    $statement = $_POST['statement'];
}

// Check if case_id is empty
if(empty($_POST['caseid'])){
    array_push($errors, "You need to enter complainant details before you are allowed to enter the action diary");
} else { 
    $case_id = $_POST['caseid'];
}

// Check if crime is empty
if(empty($_POST['crime'])){
    array_push($errors, "You need to go back and select crime type details before you are allowed to enter the action diary");
} else { 
    $crime = $_POST['crime'];
}

// Handle audio file upload if present
if(isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
    $audio_data = file_get_contents($_FILES['audio']['tmp_name']);
    $audio_type = $_FILES['audio']['type'];
    $audio_name = $_FILES['audio']['name'];
}

if($errors) {
    $output['error'] = true;
    $output['messages'] = $errors;
} else {
    // Prepare SQL query to insert case details and optional audio data
    $sql = "INSERT INTO case_table 
            (case_id, diaryofaction, staffid, case_type, date_added, audio_data, audio_type, audio_name)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

    // Insert into the database
    $success = $db->insertRow($sql, [
        $case_id, 
        $statement, 
        $staffid, 
        $crime, 
        $date,
        $audio_data,
        $audio_type,
        $audio_name
    ]);

    if($success) {
        $output['url'] = 'index.php';
    } else {
        $output['error'] = true;
        $output['message'] = 'Database insertion failed.';
    }
}

echo json_encode($output);

$db->Disconnect();
?>

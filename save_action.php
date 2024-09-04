<?php
require_once('database/Database.php');
$db = new Database(); 

if(session_status() == PHP_SESSION_NONE) {
    include('session.php');
    include('dbconnect.php');
    date_default_timezone_set("Africa/Accra"); 
}

$date = date("Y-m-d H:i:s");

// Array to handle the error messages
$errors = array();

// Array to hold the JSON encoded data
$output = array('error' => false, 'messages' => array());

// Variables to hold the form data
$statement = ''; 
$case_id = ''; 
$staffid = $session_id; 
$crime = '';

// Validate input data
if (empty($_POST['statement'])) {
    array_push($errors, "The field cannot be empty, ensure it is entered.");
} else { 
    $statement = htmlspecialchars($_POST['statement']);
}

if (empty($_POST['caseid'])) {
    array_push($errors, "You need to enter complainant details before you are allowed to enter the action diary.");
} else { 
    $case_id = htmlspecialchars($_POST['caseid']);
}

if (empty($_POST['crime'])) {
    array_push($errors, "You need to go back and select crime type details before you are allowed to enter the action diary.");
} else { 
    $crime = htmlspecialchars($_POST['crime']);
}

if (!empty($errors)) {
    $output = array('error' => true, 'messages' => $errors);
} else {  
    $sql = "INSERT INTO case_table (case_id, diaryofaction, staffid, case_type, date_added)
            VALUES(?,?,?,?,?);";
    $success = $db->insertRow($sql, [$case_id, $statement, $staffid, $crime, $date]);

    if ($success) {
        $output['url'] = 'index.php';
    } else {
        $output = array('error' => true, 'messages' => ["An error occurred while saving the data."]);
    }
}

echo json_encode($output);
$db->Disconnect();
?>

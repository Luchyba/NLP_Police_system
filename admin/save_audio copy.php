<?php
include('dbconnect.php'); // Include your database connection file

// Disable error reporting output to prevent accidental output before JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

ob_start(); // Start output buffering to capture any unintended output

$response = array('status' => 'error', 'message' => 'Unknown error occurred'); // Default response

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
            // Fetch the audio file data
            $audioData = file_get_contents($_FILES['audio']['tmp_name']);
            $audioType = $_FILES['audio']['type'];
            $audioName = $_FILES['audio']['name'];
            $caseid = isset($_POST['caseid']) ? htmlspecialchars($_POST['caseid']) : '';

            if (!empty($caseid) && $audioData !== false) {
                // Prepare SQL statement to update the case_table with the audio data
                $stmt = $conn->prepare("UPDATE `case_table` SET audio_data = ?, audio_type = ?, audio_name = ? WHERE caseid = ?");
                $stmt->bind_param("ssss", $audioData, $audioType, $audioName, $caseid);

                // Execute the query and check for success
                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Audio successfully uploaded and saved to the database.';
                } else {
                    $response['message'] = 'Database update failed.';
                    $response['sql_error'] = $stmt->error;
                }

                // Close the statement
                $stmt->close();
            } else {
                $response['message'] = 'Invalid case ID or audio data.';
            }
        } else {
            $response['message'] = 'File upload error: ' . $_FILES['audio']['error'];
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response['message'] = 'Exception occurred: ' . $e->getMessage();
}

ob_clean(); // Clean the output buffer to remove any accidental output

header('Content-Type: application/json');
echo json_encode($response); // Ensure a JSON response is always sent

// Close the database connection
$conn->close();
?>

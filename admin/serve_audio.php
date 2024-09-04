<?php
require_once('dbconnect.php');

if (isset($_GET['case_id'])) {
    $case_id = mysqli_real_escape_string($dbcon, $_GET['case_id']);

    // Query to get audio file details
    $query = mysqli_query($dbcon, "SELECT audio_data, audio_type, audio_name FROM case_table WHERE case_id='$case_id'");
    if ($row = mysqli_fetch_assoc($query)) {
        header('Content-Type: ' . $row['audio_type']);
        header('Content-Disposition: inline; filename="' . $row['audio_name'] . '"');
        echo $row['audio_data'];
    } else {
        echo 'No audio found for this case.';
    }
} else {
    echo 'No case ID specified.';
}

mysqli_close($dbcon);
?>

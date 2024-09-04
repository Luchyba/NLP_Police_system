<?php 
// Include database connection
require_once('dbconnect.php');

// Get parameters from the URL with basic sanitization
$get_id = isset($_GET['id']) ? mysqli_real_escape_string($dbcon, $_GET['id']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($dbcon, $_GET['status']) : '';

// Include other required files
include('header.php');

// Construct the URL for the audio file
$audio_url = 'serve_audio.php?case_id=' . urlencode($get_id);
?>

<div class="container-fluid">
    <div class="row">
        <?php include('menubar.php') ?>
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <!-- Buttons for printing and additional actions -->
            <a href="#" onclick="window_print()" class="btn btn-info mb-3">
                <i class="icon-print icon-large"></i> Print
            </a>

            <a href="investigation.php?edit=<?php echo htmlspecialchars($get_id); ?>" class="btn btn-info mb-3">
                <i class="icon-print icon-large"></i> Investigation Statement
            </a>

            <a href="assigncase.php?caseid=<?php echo htmlspecialchars($get_id); ?>" class="btn btn-info mb-3">
                <i class="icon-print icon-large"></i> 
                <?php echo ($status == '' || $status == 'Not Yet') ? 'Assign This Case to CID Officer' : 'Change CID Officer'; ?>
            </a>

            <!-- Case Details Panel -->
            <div class="panel panel-success" id="outprint">
                <div class="panel-heading">
                    <h3 class="panel-title">Case Details</h3>
                </div>
                <div class="panel-body">
                    <br />

                    <!-- Complainant Details -->
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Complainant Details
                            </h3>
                        </div>
                        <div class="panel-body">
                            <?php
                            // Fetch complainant details from the database
                            $query = mysqli_query($dbcon, "SELECT * FROM complainant WHERE case_id='$get_id'");
                            if ($row = mysqli_fetch_array($query)) {
                            ?>
                                <table class="table">
                                    <tr><td>Case Number:</td><td><?php echo htmlspecialchars($get_id); ?></td></tr>
                                    <tr><td>Name:</td><td><?php echo htmlspecialchars($row['comp_name']); ?></td></tr>
                                    <tr><td>Gender:</td><td><?php echo htmlspecialchars($row['gender']); ?></td></tr>
                                    <tr><td>Age:</td><td><?php echo htmlspecialchars($row['age']); ?></td></tr>
                                    <tr><td>Occupation:</td><td><?php echo htmlspecialchars($row['occupation']); ?></td></tr>
                                    <tr><td>Tel:</td><td><?php echo htmlspecialchars($row['tel']); ?></td></tr>
                                    <tr><td>Region:</td><td><?php echo htmlspecialchars($row['region']); ?></td></tr>
                                    <tr><td>District:</td><td><?php echo htmlspecialchars($row['district']); ?></td></tr>
                                    <tr><td>Location:</td><td><?php echo htmlspecialchars($row['loc']); ?></td></tr>
                                    <tr><td>ID Type:</td><td><?php echo htmlspecialchars($row['id_type']); ?></td></tr>
                                    <tr><td>ID Number:</td><td><?php echo htmlspecialchars($row['id_number']); ?></td></tr>
                                    <tr><td>ID Image:</td>
                                        <td>
                                            <?php if (!empty($row['id_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($row['id_image']); ?>" alt="ID Image" style="max-width: 200px;">
                                            <?php else: ?>
                                                <p>No image available.</p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php } else { ?>
                                <p>No complainant details found for this case.</p>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Case Details -->
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h3 class="panel-title">Case Details</h3>
                        </div>
                        <div class="panel-body">
                            <table id="myTable-party" class="table table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><center>Crime Type</center></th>
                                        <th><center>Diary of Action</center></th>
                                        <th><center>Time Reported</center></th>
                                        <th><center>NCO</center></th>
                                        <th><center>CID</center></th>
                                        <th><center>Status</center></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch case details from the database
                                    $query = mysqli_query($dbcon, "SELECT * FROM case_table WHERE case_id='$get_id'");
                                    while ($row = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['case_type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['diaryofaction']); ?></td>
                                            <td><?php echo htmlspecialchars($row['date_added']); ?></td>
                                            <td><?php echo htmlspecialchars($row['staffid']); ?></td>
                                            <td><?php echo htmlspecialchars($row['cid']); ?></td>
                                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Audio Playback Button -->
                    <div class="mb-3">
                        <audio id="caseAudio" controls style="width: 100%; max-width: 600px;">
                            <source src="<?php echo $audio_url; ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                        <button class="btn btn-primary mt-2" onclick="toggleAudio()">
                            <i class="glyphicon glyphicon-play"></i> Play Audio
                        </button>
                    </div>

                    <!-- Return Home Button -->
                    <center>
                        <a href="caseview.php" class="btn btn-success">Return Home
                            <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
                        </a>
                    </center>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

<?php include('scripts.php') ?>

<script type="text/javascript">
    // Function to print the case details
    function window_print() {
        var _head = document.head.cloneNode(true);
        var _p = document.getElementById('outprint').cloneNode(true);
        var _html = document.createElement('div');
        _html.appendChild(_head);
        _html.innerHTML += "<h3 class='text-center'>CRIME RECORDS MANAGEMENT SYSTEM</h3>";
        _html.appendChild(_p);
        var nw = window.open("", "_blank", "width=900,height=800");
        nw.document.write(_html.innerHTML);
        nw.document.close();
        nw.print();
        setTimeout(() => {
            nw.close();
        }, 500);
    }

    // Function to toggle audio play/pause
    function toggleAudio() {
        var audio = document.getElementById('caseAudio');
        if (audio.paused) {
            audio.play();
            document.querySelector('button[onclick="toggleAudio()"]').innerHTML = '<i class="glyphicon glyphicon-pause"></i> Pause Audio';
        } else {
            audio.pause();
            document.querySelector('button[onclick="toggleAudio()"]').innerHTML = '<i class="glyphicon glyphicon-play"></i> Play Audio';
        }
    }
</script>
</body>
</html>

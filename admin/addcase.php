<?php
include ('header.php');
include ('dbconnect.php');

// Validate and sanitize input parameters
$caseid = isset($_GET['caseid']) ? htmlspecialchars($_GET['caseid']) : '';
$staffid = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
$crime = isset($_GET['crimetype']) ? htmlspecialchars($_GET['crimetype']) : '';

// Check if essential parameters are missing
if (empty($caseid) || empty($staffid) || empty($crime)) {
    die("Missing essential parameters. Please go back and try again.");
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<div class="container-fluid">
    <?php include ('menubar.php'); ?>

    <div class="container-fluid">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <ul class="list-group" id="myinfo">
                <!-- Error messages will be appended here -->
            </ul>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Details of Action</h3>
                </div>
                <div class="panel-body">
                    <div class="container-fluid">
                        <form class="form-horizontal" id="addaction" role="form">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="caseid">Case Number:</label>
                                        <input type="hidden" name="staffid" value="<?php echo $staffid; ?>">
                                        <input type="text" readonly class="form-control" name="caseid" value="<?php echo $caseid; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="crime">Crime Type:</label>
                                        <input type="text" readonly class="form-control" name="crime" value="<?php echo $crime; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="statement">Diary of Action:</label>
                                        <textarea name="statement" id="statement" class="form-control" required></textarea>
                                        <div class="input-group-append mt-2">
                                            <button class="btn btn-outline-secondary" type="button" id="voiceButton">
                                                <i class="fa-solid fa-microphone"></i>
                                            </button>
                                            <button id="stopButton" class="btn btn-outline-danger" type="button" disabled>
                                                <i class="fa-solid fa-microphone-slash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="save_union" class="btn btn-success form-control">Save
                                    <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

<?php include ('scripts.php'); ?>
<script type="text/javascript">
    $(document).on('submit', '#addaction', function (event) {
        event.preventDefault();
        $(".list-group-item").remove(); // Clear previous messages
        var formData = $(this).serialize();

        $.ajax({
            url: 'save_action.php',
            type: 'post',
            data: formData,
            dataType: 'json',  // Ensure the data type is set to JSON
            success: function (response) {
                console.log('AJAX response:', response);  // Debugging output

                if (response.error) {
                    if (response.errors && Array.isArray(response.errors)) {
                        response.errors.forEach(function(error) {
                            $('#myinfo').append('<li class="list-group-item alert alert-danger">' + error + '</li>');
                        });
                    } else {
                        $('#myinfo').append('<li class="list-group-item alert alert-danger">An unexpected error occurred.</li>');
                    }
                } else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Your Case Saved',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    $('textarea[name=statement]').val('');  // Clear the textarea after success

                    setTimeout(function () {
                        window.location = 'index.php';
                    }, 900);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);  // Log any AJAX errors
                $('#myinfo').append('<li class="list-group-item alert alert-danger">There was an error processing your request. Please try again later.</li>');
            }
        });
    });

    // Voice Recording and Speech Recognition
    document.addEventListener('DOMContentLoaded', function () {
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    var speechRecognition = new SpeechRecognition();
    speechRecognition.continuous = false;
    speechRecognition.interimResults = false;

    var voiceButton = document.getElementById('voiceButton');
    var stopButton = document.getElementById('stopButton');
    var statementTextarea = document.getElementById('statement');
    var mediaRecorder;
    var audioChunks = [];

    voiceButton.addEventListener('click', function () {
        voiceButton.disabled = true;
        stopButton.disabled = false;

        try {
            speechRecognition.start();
            console.log('Speech recognition started.');
        } catch (e) {
            console.error('Speech recognition error:', e);
        }

        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(function (stream) {
                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.start();
                console.log('Media recording started.');

                mediaRecorder.ondataavailable = function (event) {
                    if (event.data.size > 0) {
                        audioChunks.push(event.data);
                        console.log('Audio data available:', event.data);
                    }
                };

                mediaRecorder.onstop = function () {
                    var audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                    var audioFile = new File([audioBlob], 'recording.wav', { type: 'audio/wav' });
                    audioChunks = [];

                    // Create a FormData object to append the audio file
                    var formData = new FormData(document.getElementById('addaction'));
                    formData.append('audio', audioFile);

                    // Send the FormData object to the server
                    $.ajax({
                        url: 'save_action.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (response) {
                            console.log('Server Response:', response);

                            if (response.error) {
                                // Handle errors
                                if (response.messages) {
                                    response.messages.forEach(function(error) {
                                        $('#myinfo').append('<li class="list-group-item alert alert-danger">' + error + '</li>');
                                    });
                                } else {
                                    $('#myinfo').append('<li class="list-group-item alert alert-danger">An unexpected error occurred.</li>');
                                }
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Your Case Saved',
                                    showConfirmButton: false,
                                    timer: 3000
                                });

                                $('textarea[name=statement]').val('');  // Clear the textarea after success

                                setTimeout(function () {
                                    window.location = response.url;
                                }, 900);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            $('#myinfo').append('<li class="list-group-item alert alert-danger">There was an error processing your request. Please try again later.</li>');
                        }
                    });
                };
            })
            .catch(function (err) {
                console.error('getUserMedia error:', err);
                voiceButton.disabled = false;
                stopButton.disabled = true;
            });
    });

    stopButton.addEventListener('click', function () {
        voiceButton.disabled = false;
        stopButton.disabled = true;

        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }

        try {
            speechRecognition.stop();
            console.log('Speech recognition stopped.');
        } catch (e) {
            console.error('Speech recognition stop error:', e);
        }
    });

    speechRecognition.onresult = function (event) {
        var result = event.results[0][0].transcript;
        statementTextarea.value += ' ' + result;
    };

    speechRecognition.onerror = function (event) {
        console.error('Speech recognition error:', event.error);
    };
});

</script>
</body>
</html>

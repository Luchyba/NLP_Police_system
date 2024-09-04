<?php 
include('session.php');
include('header.php');
include('dbconnect.php');
?>

<div class="container-fluid">
    <?php include('menubar.php'); ?>

    <?php 
    $trans_id = uniqid();
    ?>

    <div class="container-fluid">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <ul class="list-group" id="myinfo">
                <li class="list-group-item" id="mylist"></li>
            </ul>
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">Complainant Details</h3>
                </div>
                <div class="panel-body">
                    <div class="container-fluid">
                        <form class="form-horizontal" id="addcase" role="form">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name of Complainant:</label>
                                        <input type="hidden" name="uid" value="<?php echo htmlspecialchars($trans_id); ?>">
                                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tel">Tel Phone:</label>
                                        <input type="text" name="tel" class="form-control" id="tel" maxlength="10" placeholder="Enter Number" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nid">Occupation:</label>
                                        <input type="text" name="occ" class="form-control" id="nid" placeholder="Enter Occupation" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Gender:</label>
                                        <select class="form-control" name="gender" id="gender" required>
                                            <option selected="selected" value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="age">Age:</label>
                                        <input type="number" name="age" class="form-control" id="age" placeholder="Age" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="addrs">Address:</label>
                                        <input type="text" name="addrs" class="form-control py-4" id="addrs" placeholder="Address" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country">Region:</label>
                                        <script type="text/javascript" src="js/regions.js"></script>
                                        <select class="form-control" required onchange="print_state('state',this.selectedIndex);" id="country" name="region"></select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="state">District/Municipal:</label>
                                        <select required class="form-control" name="district" id="state"></select>
                                        <script language="javascript">print_country("country");</script>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="loc">Location:</label>
                                        <input type="text" name="loc" class="form-control" id="loc" placeholder="Enter Location" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="crime">Select Crime Type:</label>
                                        <select class="form-control" name="crime_type" id="crime" required>
                                            <option selected="selected" value="">Select</option>
                                            <?php
                                            $sql = mysqli_query($dbcon, "select * from crime_type");
                                            while($row = mysqli_fetch_assoc($sql)) {
                                                echo '<option value="' . htmlspecialchars($row['des']) . '">' . htmlspecialchars($row['des']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="">ID Type:</label>
									<select class="form-control" name="id_type" id="id_type" required>
										<option value="">Select ID Type</option>
										<option value="Ghana Card">Ghana Card</option>
										<option value="Voter ID">Voter ID</option>
										<option value="Insurance Card">Insurance Card</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">ID Number:</label>
									<input type="text" name="id_number" class="form-control" id="id_number" placeholder="Enter ID Number" required>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="">Upload ID:</label>
                                    <div>
                                    <video id="video" width="320" height="240" autoplay></video>
                                    <button type="button" id="snap">Snap Photo</button>
                                    <canvas id="canvas" width="320" height="240"></canvas>
                                </div>
                                <input type="hidden" name="id_image" class="form-control" id="id_image">
									<!-- <input type="hidden" name="id_image" class="form-control" id="id_image" accept="image/*" required> -->
								</div>
							</div>
						</div>

                            <div class="form-group">
                                <button type="submit" name="save_case" class="btn btn-success form-control">Save and Continue
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

<?php include('scripts.php'); ?>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        var video = document.getElementById('video');
        var canvas = document.getElementById('canvas');
        var snapButton = document.getElementById('snap');
        var idCardData = document.getElementById('id_image');
        var context = canvas.getContext('2d');
        var stream; // Variable to store the video stream

        // Access the camera
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(streamData) {
                stream = streamData; // Store the stream data
                video.srcObject = stream;
                video.play();
            })
            .catch(function(error) {
                console.error("Error accessing camera: ", error);
            });
        }

        // Capture the photo
        snapButton.addEventListener('click', function () {
            context.drawImage(video, 0, 0, 320, 240);
            var imageData = canvas.toDataURL('image/png');
            idCardData.value = imageData; // Store image data in hidden input

            // Stop the camera stream
            if (stream) {
                let tracks = stream.getTracks();
                tracks.forEach(track => track.stop());
                video.srcObject = null;
            }
        });
    });
	$(document).on('submit', '#addcase', function(event) {
		event.preventDefault();
		$(".list-group-item").remove();
		
		var formData = new FormData(this); // Updated for file upload

		$.ajax({
			url: 'savecompl.php',
			type: 'post',
			data: formData,
			contentType: false, // Added for file upload
			processData: false, // Added for file upload
			dataType: 'JSON',
			success: function(response){
				if(response.error){
					var len = response[0].length;
					for(var i=0; i<len; i++){
						$('#myinfo').append('<li class="list-group-item alert alert-danger"> ' + response[0][i] + '</li>');
					}
				} else {
					window.location = response.url;
				}
			}
		});
	});
</script>
</body>
</html>

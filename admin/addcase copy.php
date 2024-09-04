<?php
include ('header.php');
include ('dbconnect.php');
?>

<div class="container-fluid">
	<?php include ('menubar.php'); ?>

	<?php
	$caseid = $_GET['caseid'];
	$staffid = $_GET['id'];
	$crime = $_GET['crimetype'];
	?>

	<div class="container-fluid">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<ul class="list-group" id="myinfo">
				<li class="list-group-item" id="mylist"></li>
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
										<input type="hidden" name="staffid"
											value="<?php echo htmlspecialchars($staffid); ?>">
										<input type="text" readonly class="form-control" name="caseid"
											value="<?php echo htmlspecialchars($caseid); ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="crime">Crime Type:</label>
										<input type="text" class="form-control" readonly name="crime"
											value="<?php echo htmlspecialchars($crime); ?>">
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="statement">Diary of Action:</label>
										<textarea name="statement" id="statement" class="form-control"
											required></textarea>
										<div class="input-group-append">
											<button class="btn btn-outline-secondary" type="button" id="voiceButton">
												<!-- <i class="fa fa-microphone"></i> -->
												<i class="fa-solid fa-microphone"></i>
											</button>
											<button id="stopButton" disabled><i class="fa-solid fa-microphone-slash"></i></button>
										</div>
										<input type="hidden" name="staffid"
											value="<?php echo htmlspecialchars($staffid); ?>">
										<input type="hidden" name="caseid"
											value="<?php echo htmlspecialchars($caseid); ?>">
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
		$(".list-group-item").remove();
		var formData = $(this).serialize();
		$.ajax({
			url: 'save_action.php',
			type: 'post',
			data: formData,
			dataType: 'JSON',
			success: function (response) {
				if (response.error) {
					var len = response[0].length;
					for (var i = 0; i < len; i++) {
						$('#myinfo').append('<li class="list-group-item alert alert-danger">' + response[0][i] + '</li>');
					}
				} else {
					Swal.fire({
						position: 'top-end',
						icon: 'success',
						title: 'Your Case Saved',
						showConfirmButton: false,
						timer: 3000
					});
					$('textarea[name=statement]').val('');
					setTimeout(function () {
						window.location = 'addcompl.php';
					}, 900);
				}
			}
		});
	});
	// Voice icon

	document.addEventListener('DOMContentLoaded', function () {
		var speechRecognition = new webkitSpeechRecognition();
		var voiceButton = document.getElementById('voiceButton');
		var statementTextarea = document.getElementById('statement');
		var stopButton = document.getElementById('stopButton');
		var translateButton = document.getElementById('translateButton');

		// For Audio Recording
		var mediaRecorder;
            var audioChunks = [];

			voiceButton.addEventListener('click', function () {
                // Start Speech Recognition
                speechRecognition.start();

                // Start Audio Recording
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(function (stream) {
                        mediaRecorder = new MediaRecorder(stream);
                        mediaRecorder.start();
                        stopButton.disabled = false;

                        mediaRecorder.ondataavailable = function (event) {
                            audioChunks.push(event.data);
                        };

                        mediaRecorder.onstop = function () {
                            var audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                            audioChunks = [];

                            // Send audio to the server
                            var formData = new FormData();
                            formData.append('audio', audioBlob, 'recording.wav');

                            // Send a POST request to the server
                            fetch('save_audio.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.text())
                            .then(data => {
                                console.log('Server Response:', data);
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                        };
                    });
            });

            stopButton.addEventListener('click', function () {
                // Stop Recording
                mediaRecorder.stop();
                speechRecognition.stop();
                stopButton.disabled = true;
            });

            speechRecognition.onresult = function (event) {
                var result = event.results[0][0].transcript;
                statementTextarea.value = result;
            };

            speechRecognition.onerror = function (event) {
                console.error('Speech recognition error detected: ' + event.error);
            };
        });

// --------------------------------update ----------------------------
		// Voice Recognition
	// 	voiceButton.addEventListener('click', function () {
	// 		speechRecognition.start();

	// 		speechRecognition.onresult = function (event) {
	// 			var result = event.results[0][0].transcript;
	// 			statementTextarea.value = result;
	// 		};

	// 		speechRecognition.onerror = function (event) {
	// 			console.error('Speech recognition error detected: ' + event.error);
	// 		};
	// 	});

	// 	// Translation (Example using Google Translate API)
	// 	translateButton.addEventListener('click', function () {
	// 		var textToTranslate = statementTextarea.value;
	// 		var targetLanguage = 'en'; // Example target language code (e.g., 'en' for English)

	// 		// Example using Google Translate API (replace with your API key and endpoint)
	// 		fetch(`https://translation.googleapis.com/language/translate/v2?key=YOUR_API_KEY&q=${encodeURIComponent(textToTranslate)}&target=${targetLanguage}`, {
	// 			method: 'POST'
	// 		})
	// 			.then(response => response.json())
	// 			.then(data => {
	// 				if (data && data.data && data.data.translations && data.data.translations[0] && data.data.translations[0].translatedText) {
	// 					statementTextarea.value = data.data.translations[0].translatedText;
	// 				} else {
	// 					console.error('Translation failed');
	// 				}
	// 			})
	// 			.catch(error => console.error('Translation error:', error));
	// 	});
	// });


</script>
</body>

</html>
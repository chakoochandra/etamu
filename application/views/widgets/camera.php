<link rel="icon" href="data:;base64,iVBORw0KGgo=">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
	.container-frame {
		width: 100%;
		position: relative;
		padding-top: 56.25%;
		/* 16:9 aspect ratio */
	}

	.webcam-frame {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: #000;
		/* just for illustration */
	}

	video {
		position: absolute;
		top: 1px;
	}

	.camera-controls {
		position: absolute;
		bottom: 0;
		width: 100%;
		display: flex;
		justify-content: center;
		align-items: center;
		z-index: 10;
		/* Higher than webcam-container */
		background-color: rgba(0, 0, 0, 0.5);
		/* Optional: semi-transparent background */
		padding: 5px;
	}
</style>

<main id="main-webcam-<?php echo $name ?>-<?php echo $class ?>">
	<div class="container-frame">
		<div class="webcam-frame row p-0 m-0">
			<div id="webcam-<?php echo $name ?>-<?php echo $class ?>-container" class="webcam-<?php echo $name ?>-<?php echo $class ?>-container w-100 p-0 m-0 d-none">
				<video id="webcam-<?php echo $name ?>-<?php echo $class ?>" class="w-100 h-100" autoplay playsinline></video>
				<canvas id="canvas-<?php echo $name ?>-<?php echo $class ?>" class="d-none w-100"></canvas>
				<div class="flash-<?php echo $name ?>-<?php echo $class ?>"></div>
				<audio id="snapSound-<?php echo $name ?>-<?php echo $class ?>" src="<?php echo base_url('assets/vendor/webcam-easy/demo/snap.wav') ?>" preload="auto"></audio>
			</div>
			<div id="error-message-<?php echo $name ?>-<?php echo $class ?>" class="error-message direct-chat-text bg-danger d-none">
				Gagal memulai kamera, pastikan mengakses <?php echo APP_SHORT_NAME ?> menggunakan <strong>https</strong> dan izinkan untuk mengakses kamera.<br /><br />
				<strong><a href="<?php echo str_replace('http://', 'https://', base_url('site')) ?>" class="text-white">Klik di sini untuk mereload <?php echo APP_SHORT_NAME ?> menggunakan https</a></strong>
			</div>
			<div id="cameraControls-<?php echo $name ?>-<?php echo $class ?>" class="camera-controls cameraControls-<?php echo $name ?>-<?php echo $class ?> d-flex justify-content-center align-items-center w-100">
				<a href="#" id="open-camera-<?php echo $name ?>-<?php echo $class ?>" title="Buka Kamera" class="btn btn-outline-success w-100"><i class="fa fa-camera" aria-hidden="true"></i> Buka Kamera</a>

				<a href="#" id="exit-app-<?php echo $name ?>-<?php echo $class ?>" title="Tutup Kamera" class="mode-capture-<?php echo $name ?>-<?php echo $class ?> btn btn-outline-danger w-50 d-none justify-content-center align-items-center"><i class="fa fa-close" aria-hidden="true"></i>&nbsp;Batal</a>
				<!-- <a href="#" id="camera-flip" title="Switch Kamera" class="mode-capture"><i class="material-icons">flip_camera_android</i></a> -->
				<a href="#" id="take-photo-<?php echo $name ?>-<?php echo $class ?>" title="Ambil Foto" class="mode-capture-<?php echo $name ?>-<?php echo $class ?> btn btn-outline-success w-50 d-none justify-content-center align-items-center"><i class="fa fa-camera" aria-hidden="true"></i>&nbsp;Ambil Foto</a>
			</div>
		</div>
	</div>
</main>
<input type="hidden" id="hidden-<?php echo $name ?>-<?php echo $class ?>" name="<?php echo $name ?>" value="<?php echo $value ?>" />

<script>
	$(document).ready(function($) {
		var canvas = document.getElementById('canvas-<?php echo $name ?>-<?php echo $class ?>');
		var webcam = new Webcam(document.getElementById('webcam-<?php echo $name ?>-<?php echo $class ?>'), 'user', canvas, document.getElementById('snapSound-<?php echo $name ?>-<?php echo $class ?>'));
		var picture;

		if ('<?php echo $value ?>') {
			displayBase64ImageOnCanvas('canvas-<?php echo $name ?>-<?php echo $class ?>', '<?php echo $value ?>');

			afterTakePhoto();
		}

		$("#open-camera-<?php echo $name ?>-<?php echo $class ?>").click(function(e) {
			e.preventDefault();

			webcam.start()
				.then(result => {
					cameraStarted();
				})
				.catch(err => {
					displayError(err);
				});
		});

		$("#exit-app-<?php echo $name ?>-<?php echo $class ?>").click(function(e) {
			e.preventDefault();

			webcam.stop();
			cameraStopped();
		});

		$('#camera-flip-<?php echo $name ?>-<?php echo $class ?>').click(function(e) {
			e.preventDefault();

			webcam.flip();
			webcam.start();
		});

		$("#take-photo-<?php echo $name ?>-<?php echo $class ?>").click(function(e) {
			e.preventDefault();

			beforeTakePhoto();

			picture = webcam.snap();
			var compressedImage = canvas.toDataURL('image/png', 0.1);
			$('#hidden-<?php echo $name ?>-<?php echo $class ?>').val(compressedImage);

			afterTakePhoto();
		});

		$('form').on('submit', function() {
			var videoElement = $('#webcam-<?php echo $name ?>-<?php echo $class ?>');
			if (videoElement.is(":visible")) {
				picture = webcam.snap();
				var compressedImage = canvas.toDataURL('image/png', 0.1);
				$('#hidden-<?php echo $name ?>-<?php echo $class ?>').val(compressedImage);
				webcam.stop();
			}
		});

		function afterTakePhoto() {
			webcam.stop();
			$("#webcam-<?php echo $name ?>-<?php echo $class ?>-container").removeClass("d-none");
			$('#canvas-<?php echo $name ?>-<?php echo $class ?>').removeClass('d-none');
			$('.mode-capture-<?php echo $name ?>-<?php echo $class ?>').removeClass('d-flex');
			$('.mode-capture-<?php echo $name ?>-<?php echo $class ?>').addClass('d-none');
			$('#webcam-<?php echo $name ?>-<?php echo $class ?>').addClass('d-none');
			$("#open-camera-<?php echo $name ?>-<?php echo $class ?>").removeClass("d-none");
		}

		function beforeTakePhoto() {
			$('.flash-<?php echo $name ?>-<?php echo $class ?>')
				.show()
				.animate({
					opacity: 0.3
				}, 500)
				.fadeOut(500)
				.css({
					'opacity': 0.7
				});
			$('#cameraControls-<?php echo $name ?>-<?php echo $class ?>').addClass('d-none');
		}

		function displayError(err = '') {
			if (err != '') {
				console.log(err);
				$("#error-message-<?php echo $name ?>-<?php echo $class ?>").html(err);
			}
			$("#error-message-<?php echo $name ?>-<?php echo $class ?>").removeClass("d-none");
		}

		function cameraStarted() {
			$("#webcam-<?php echo $name ?>-<?php echo $class ?>").removeClass("d-none");
			$('#canvas-<?php echo $name ?>-<?php echo $class ?>').addClass('d-none');
			$("#webcam-<?php echo $name ?>-<?php echo $class ?>-container").removeClass("d-none");
			$(".mode-capture-<?php echo $name ?>-<?php echo $class ?>").removeClass("d-none");
			$(".mode-capture-<?php echo $name ?>-<?php echo $class ?>").addClass("d-flex");
			$("#open-camera-<?php echo $name ?>-<?php echo $class ?>").addClass("d-none");
			$("#error-message-<?php echo $name ?>-<?php echo $class ?>").addClass("d-none");
			$('.flash-<?php echo $name ?>-<?php echo $class ?>').hide();

			// if (webcam.webcamList.length > 1) {
			// 	$("#camera-flip").removeClass('d-none');
			// }
		}

		function cameraStopped() {
			$(".mode-capture-<?php echo $name ?>-<?php echo $class ?>").addClass("d-none");
			$(".mode-capture-<?php echo $name ?>-<?php echo $class ?>").removeClass("d-flex");
			$("#open-camera-<?php echo $name ?>-<?php echo $class ?>").removeClass("d-none");
			$("#error-message-<?php echo $name ?>-<?php echo $class ?>").addClass("d-none");

			if (hasImageContent($('#canvas-<?php echo $name ?>-<?php echo $class ?>')[0])) {
				afterTakePhoto();
			} else {
				$("#webcam-<?php echo $name ?>-<?php echo $class ?>-container").addClass("d-none");
			}

			// $("#camera-flip").addClass('d-none');
		}

		function hasImageContent(canvas) {
			var context = canvas.getContext('2d');
			var imgData = context.getImageData(0, 0, canvas.width, canvas.height);
			var hasContent = false;

			for (var i = 0; i < imgData.data.length; i += 4) {
				if (imgData.data[i + 3] !== 0) { // Check alpha value
					hasContent = true;
					break;
				}
			}
			return hasContent;
		}

		function displayBase64ImageOnCanvas(canvasId, base64Image) {
			var canvas = document.getElementById(canvasId);
			var context = canvas.getContext('2d');
			var img = new Image();
			img.onload = function() {
				canvas.width = img.width;
				canvas.height = img.height;

				context.drawImage(img, 0, 0);

				canvas.classList.remove('d-none');
			};
			img.src = base64Image;
		}
	});
</script>
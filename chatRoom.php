<!DOCTYPE html>
<html>
<head>
	<title>Chat Room</title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<link rel="stylesheet" href="./stylesheets/style.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.js"></script>
	<script>

		$(document).ready(function(){

			function sendMessage() {
				var name = $.cookie('name');
				var msg = $('#message').val();
				var str = msg;
				socket.send(str);
				scrollDown();
			}

			function clearInput() {
				$('#message').val('');
			}

			function scrollDown() {
				var height = 0;
				$('#chat-log > div').each(function(i, value){
				    height += parseInt($(this).height());
				});

				height += '';

				$('#chat-log').animate({scrollTop: height}, 800);
			}			

			$('#button-addon2').on('click', function() {
				if($('#message').val().localeCompare('') != 0) {
					sendMessage();
					clearInput();
				}
			});

			$('#message').on('keypress', function(e) {
				if(e.which == 13 && $('#message').val().localeCompare('') != 0) {
					sendMessage();
					clearInput();
				}
			});

			if (document.cookie.indexOf('name') == -1 ) {
				var name = prompt('Enter your name');
				document.cookie = 'name=' + name;
			}
			var socket = new WebSocket('ws://localhost:8081/chatRoom/bin/chat-server.php');
			socket.onmessage = function(data) {
				var msg = JSON.parse(data.data);
				var name = $.cookie('name');
				var str = '';
				var visitor = '';

				if (msg.message.localeCompare("") == 0) {
					str = '<div class="row"><div class="col-4 offset-4 text-center p-1 mt-1 mb-1 rounded" style="background-color: rgba(220,178,57, 0.7); color: white;">' + msg.systemInfo + '</div></div>';
				} else if (msg.sender.localeCompare(name) == 0) {
					str = '<div class="row m-1"><div class="col-6 offset-6"><div id="self-box" class="p-2 mr-2 mt-1 mb-1 float-right">' + msg.message +'</div></div></div>';
				} else {
					str = '<div class="row m-1"><div class="col-6"><b id="others-name">' + msg.sender + '</b><div id="others-box" class="p-2 ml-2">' + msg.message + '</div></div></div>';
				}
				$('#chat-log').append(str);

				$.each(msg.visitors, function(index, value){
					if (value.localeCompare(name) == 0) {
						visitor += '<i class="fas fa-user text-dark"></i><b class="text-uppercase text-dark"> ' + value + '</b><br>';
					} else {
						visitor += '<i class="fas fa-user"></i> ' + value + '<br>';
					}
				});
				$('#visitor').html(visitor);
				scrollDown();
			}

		});



	</script>
</head>
<body>
<div class="container">
	<div class="row">

		<!-- Left -->
		<div id="left" class="col-9 rounded border border-white">

			<!-- Chat Log -->
			<div id="chat-log" class="mt-3 mb-3"></div>

			<!-- Input Box -->
			<div id="input-box" class="input-group mt-4 mb-4 align-bottom">
				<input id="message" type="text" class="form-control" aria-describedby="button-addon2">
				<div class="input-group-append bg-white rounded">
					<button class="btn btn-outline-secondary" type="button" id="button-addon2">Send</button>
				</div>
			</div>


		</div>


		<!-- Right -->
		<div id="right" class="col-3 rounded border border-white font-weight-bold text-secondary">
			<h2 class="mt-3 text-uppercase">Visitors</h2>
			<div id="visitor" class="mt-3"></div>
		</div>
	</div>
</div>
</body>
</html>
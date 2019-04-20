
var socket = new WebSocket('ws://localhost:8081/chatRoom/bin/chat-server.php');

$(document).ready(function(){
	function sendMessage() {
		var name = $.cookie('name');
		var msg = $('#message').val();
		var str = JSON.stringify({
			name: name,
			message: msg
		});
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

	function nameSetting(){
		var name =  $('#input-name').val();
		if ( name != '' && name != null ) {
			var str = JSON.stringify({
				name: name,
				message: 'Name Setting'
			});

			// if web socket is connecting, send message after 1 second
			// otherwise, send message directly

			if (socket.readyState == 0) {
				setTimeout(function(){
					socket.send(str);
				}, 1000);
			} else {
				socket.send(str);
			}
		}
	};

	if ( $.cookie('name') !== undefined ) {
		$('#cover').css('display', 'none');
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

	$(window).on('beforeunload', function() {
		return '';
	});

	$(window).on('unload', function() {
		$.removeCookie('name');
	});

	$('#submit').on('click', function() {
		nameSetting();
	});

	$('#input-name').on('keypress', function(e) {
		if(e.which == 13 && $('#input-name').val().localeCompare('') != 0) {
			nameSetting();
		}
	});

	socket.onmessage = function(raw_data) {
		var data = JSON.parse(raw_data.data);

		if (data.setting === 'Name Exists') {
			// print message to user
			alert('Name Exists! Please Try Again');
		} else if (data.setting === 'Name OK') {
			// set cookie & set div to none

			$.cookie('name', data.sender);
			$('#cover').css('display', 'none');
		} else {

			var name = $.cookie('name');
			var visitor = '';
			var str;

			if (data.systemInfo != '') {
				// if there is a system info

				str = '<div class="row"><div class="col-4 offset-4 text-center p-1 mt-1 mb-1 rounded" style="background-color: rgba(220,178,57, 0.7); color: white;">' + data.systemInfo + '</div></div>';

			} else if (data.sender.localeCompare(name) == 0) {
				// if receive message from sender itself

				str = '<div class="row m-1"><div class="col-6 offset-6"><div id="self-box" class="p-2 mr-2 mt-1 mb-1 float-right">' + data.message +'</div></div></div>';

			} else {
				// recieve message from other users

				str = '<div class="row m-1"><div class="col-6"><b id="others-name">' + data.sender + '</b><div id="others-box" class="p-2 ml-2">' + data.message + '</div></div></div>';

			}
			// append message to html div
			$('#chat-log').append(str);


			// show users in chat room on the right column box
			$.each(data.visitors, function(index, value){
				if (value.localeCompare(name) == 0) {
					visitor += '<i class="fas fa-user text-dark"></i><b class="text-uppercase text-dark"> ' + value + '</b><br>';
				} else {
					visitor += '<i class="fas fa-user"></i> ' + value + '<br>';
				}
			});

			$('#visitor').html(visitor);
			scrollDown();

		}

	}

	socket.onopen = function(){}

	socket.onclose = function(){}

	socket.onerror = function(){}

});

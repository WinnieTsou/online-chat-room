<?php 
// session_start();

// if ( isset($_SESSION['name']) ) {
// 	$url = 'http://' . $_SERVER['HTTP_HOST'] . 'online-chat-room/chatRoom.html';
// 	header('Location: ' . $url);
// } else if ( isset($_POST['submit'])){

// }
namespace MyApp;
use Symfony\Component\HttpFoundation\Session;

// include dirname(__DIR__) . '/vendor/symfony/http-foundation/Session/Session.php';

$session = new Session();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Chat Room Login</title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<div class="container mt-5">
		<div class="row">
			<div class="col-8 offset-2 card">
				<form action="./login.php" method="post" class="form-group card-body">
					<label class="m-2">Enter Your Name: </label>
					<input class="form-control m-2" type="text" name="name" required>
					<input class="btn btn-primary m-2" type="submit" name="submit" value="submit">
				</form>
			</div>
		</div>
	</div>
</body>
</html>
<?php
	include 'dbconn.php';
	session_start();

	$message = "";

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$username = $_POST['username'];
		$password = $_POST['password'];

		$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->store_result();

		if($stmt->num_rows == 1){
			$stmt->bind_result($id, $hashed_password);
			$stmt->fetch();

			if(password_verify($password, $hashed_password)){
				$_SESSION['user_id'] = $id;
				$_SESSION['username'] = $username;

				header("Location: dashboard.php");
				exit();
			}else{
				$message = "Invalid password!";
			}
			}else {
				$message = "Username not found!";
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container mt-5">
		<div class="card mx-auto" style="max-width: 500px;">
			<div class="card-body shadow">
				<h3 class="card-title mb-4 text-center text-primary">-Login-</h3><?php if  ($message): ?>
				<div class="alert alert-danger"><?= $message ?></div><?php endif; ?>
				<form action="" method="POST">
					<div class="mb-3">
						<label>Username</label>
						<input type="text" name="username" class="form-control" required>
					</div>
					<div class="mb-3">
						<label>Password</label>
						<input type="password" name="password" class="form-control" required>
					</div>
					<button class="btn btn-primary w-100" type="submit">Login</button>
				</form>
				<p class="mt-3 text-center">Don't have an account?<a href="register.php">Register</a></p>
				<p class="mt-3 text-center"><a href="forgot-password.php">Forgot Password</a></p>
			</div>
		</div>
	</div>
</body>
</html>
<?php
	include 'dbconn.php';
	$message = "";

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$username = $_POST['username'];
		$email = $_POST['email'];
		$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);


		$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND email = ?");
		$stmt->bind_param("ss", $username, $email);
		$stmt->execute();
		$stmt->store_result();

		if($stmt->num_rows == 1){
			$update = $conn->prepare("UPDATE users SET password = ? WHERE username = ? AND email = ?");
			$update->bind_param("sss", $new_password, $username, $email);

			if($update->execute()){
				$message = "Password updated successfully. <a href='login.php'>Login</a>";
			}else{
				$message = "Error updating password.";
			}
		}else{
			$message = "Username and Email do not match.";
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Forgot-password</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container mt-5">
		<div class="card mx-auto" style="max-width: 500px;">
			<div class="card-body shadow">
				<h3 class="card-title text-center text-primary mb-5">-Forgot Password-</h3><?php if ($message): ?>
				<div class="alert alert-info"><?= $message ?></div><?php endif; ?>
				<form action="" method="POST">
					<div class="mb-3">
						<label>Username</label>
						<input type="text" name="username" class="form-control" required>
					</div>
					<div class="mb-3">
						<label>Registered Email</label>
						<input type="email" name="email" class="form-control" required>
					</div>
					<div class="mb-3">
						<label>New Password</label>
						<input type="password" name="new_password" class="form-control" required>
					</div>
					<button class="btn btn-primary w-100" type="submit">
						Reset password
					</button>
				</form>
				<p class="mt-3 text-center"><a href="login.php">Back to Login</a></p>
			</div>
		</div>
	</div>
</body>
</html>
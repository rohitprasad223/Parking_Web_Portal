<?php
 	session_start();
 
 	if(!isset($_SESSION['username'])){
 		header("Location: login.php");
 		exit();
 	}
 	include 'dbconn.php';
 	$success = "";
 	$error = "";

 	if ($_SERVER["REQUEST_METHOD"] == "POST"){
 		$username = $_SESSION['username'];
 		$current_password = $_POST['current_password'];
 		$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
 		$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
 		$stmt->bind_param("s", $username);
 		$stmt->execute();
 		$stmt->bind_result($db_password);
 		$stmt->fetch();
 		$stmt->close();

 		if(password_verify($current_password, $db_password)){
 			$update = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
 			$update->bind_param("ss",$new_password, $username);
 			if($update->execute()){
 				$success = "Password updated successfully!";
 			}else{
 				$error = "Something went wrong!";
 			}
 		}else{
 			$error = "Current password is incorrect.";				
 		}
 	}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-container {
      max-width: 600px;
      margin: 50px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
	<div class="container mt-5">
		<a href="dashboard.php" class="btn btn-danger mb-3">← Back to Dashboard</a>
      <div class="card mx-auto" style="max-width: 600px;">
     		 <div class="card-body shadow">
	  				<h3 class="text-center text-primary">-Change Password-</h3>
						<?php if ($success): ?>
						  <div class="alert alert-success"><?php echo $success; ?></div>
						<?php elseif ($error): ?>
						  <div class="alert alert-danger"><?php echo $error; ?></div>
						<?php endif; ?>

					  <form method="POST">
					  	 <div class="mb-3">
					      <label>Username</label>
					      <input type="text" name="username" class="form-control" required>
					    </div>
					    <div class="mb-3">
					      <label>Current Password</label>
					      <input type="current_password" name="current_password" class="form-control" required>
					    </div>
					    <div class="mb-3">
					      <label>New Password</label>
					      <input type="password" name="new_password" class="form-control" required>
					    </div>
					    <button type="submit" class="btn btn-primary col-12">Update Password</button>
					  </form>
					</div>
				</div>
		</div>
	<script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted || (performance.navigation.type === 2)) {
            window.location.reload(true); 
        }
    });
</script>	
</body>
</html>
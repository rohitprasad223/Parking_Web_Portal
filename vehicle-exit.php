<?php
session_start();
 
 	if(!isset($_SESSION['username'])){
 		header("Location: login.php");
 		exit();
 	}
include('dbconn.php');

$success = "";
$error = "";

date_default_timezone_set("Asia/Kolkata");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vehicle_id'])) {
    $vehicle_id = intval($_POST['vehicle_id']);
    $payment_status = $_POST['payment_status'];
    $out_time = date("Y-m-d H:i:s");
    $status = "OUT";

    $stmt = $conn->prepare("UPDATE vehicles SET out_time = ?, status = ?, payment_status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $out_time, $status, $payment_status, $vehicle_id);

    if ($stmt->execute()) {
        $success = "Vehicle marked as exited!";
    } else {
        $error = "Failed to update vehicle exit.";
    }
}
$result = $conn->query("SELECT * FROM vehicles WHERE status = 'IN'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Exit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container mt-5">
		<a href="view-vehicle.php" class="btn btn-danger mb-3">← Back to View</a>
            <div class="card mx-auto" style="max-width: 600px;">
           	 <div class="card-body shadow">
	    		<h2 class="text-center text-primary">-Vehicle Exit-</h2>
			    <?php if ($success): ?>
			        <div class="alert alert-success"><?= $success ?></div>
			    <?php elseif ($error): ?>
			        <div class="alert alert-danger"><?= $error ?></div>
			    <?php endif; ?>
			    <form method="post">
			        <div class="mb-3">
			            <label>Select Vehicle</label>
			            <select name="vehicle_id" class="form-control" required>
			                <option value="">-- Choose Vehicle --</option>
			                <?php while ($row = $result->fetch_assoc()) : ?>
			                    <option value="<?= $row['id'] ?>">
			                        <?= $row['vehicle_number'] ?> - <?= $row['owner_name'] ?>
			                    </option>
			                <?php endwhile; ?>
			            </select>
			        </div>
			        <div class="mb-3">
			            <label>Payment Status</label>
			            <select name="payment_status" class="form-control" required>
			                <option value="Paid">Paid</option>
			                <option value="Pending">Pending</option>
			            </select>
			        </div>
			        <button type="submit" class="btn btn-primary col-12"> Exited</button>
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
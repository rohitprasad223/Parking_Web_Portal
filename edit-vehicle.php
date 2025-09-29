<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'dbconn.php';

if (!isset($_GET['id'])) {
    echo "Invalid request!";
    exit();
}

$id = intval($_GET['id']);
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $fuel_type = $_POST['fuel_type'] ?? '';
    $status = $_POST['status'] ?? '';
    $payment_status = $_POST['payment_status'] ?? '';

    $stmt = $conn->prepare("UPDATE vehicles SET  vehicle_type=?, fuel_type=?, status=?, payment_status=? WHERE id=?");
    $stmt->bind_param("ssssi", $vehicle_type, $fuel_type, $status, $payment_status, $id);

    if ($stmt->execute()) {
        $success = "Vehicle updated successfully!";
    } else {
        $error = "Update failed!";
    }
}

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();
$stmt->close();

if (!$vehicle) {
    echo "Vehicle not found!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Vehicle</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .form-container {
      max-width: 700px;
      margin: 50px auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
    <div class="container mt-5">
      <a href="view-vehicle.php" class="btn btn-danger mb-3">← Back to View</a>
        <div class="card mx-auto" style="max-width: 600px;">
          <div class="card-body">
            <h3 class="text-center text-primary">-Edit Vehicle Entry-</h3>
            <?php if ($success): ?>
              <div class="alert alert-success"><?php echo $success; ?></div>
            <?php elseif ($error): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
              <div class="mb-3">
                <label>Vehicle Type</label>
                <select name="vehicle_type" class="form-select" required>
                  <option <?php if ($vehicle['vehicle_type'] == '2 Wheeler') echo 'selected'; ?>>2 Wheeler</option>
                  <option <?php if ($vehicle['vehicle_type'] == '4 Wheeler') echo 'selected'; ?>>4 Wheeler</option>
                </select>
              </div>

              <div class="mb-3">
                <label>Fuel Type</label>
                <select name="fuel_type" class="form-select" required>
                  <option <?php if ($vehicle['fuel_type'] == 'Petrol') echo 'selected'; ?>>Petrol</option>
                  <option <?php if ($vehicle['fuel_type'] == 'Diesel') echo 'selected'; ?>>Diesel</option>
                  <option <?php if ($vehicle['fuel_type'] == 'Electric') echo 'selected'; ?>>Electric</option>
                </select>
              </div>

              <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-select" required>
                  <option <?php if ($vehicle['status'] == 'IN') echo 'selected'; ?>>IN</option>
                  <option <?php if ($vehicle['status'] == 'OUT') echo 'selected'; ?>>OUT</option>
                </select>
              </div>

              <div class="mb-3">
                  <label class="form-label">Payment Status</label>
                  <select name="payment_status" class="form-select" required>
                      <option value="PAID" <?= $vehicle['payment_status'] == 'PAID' ? 'selected' : '' ?>>PAID</option>
                      <option value="UNPAID" <?= $vehicle['payment_status'] == 'UNPAID' ? 'selected' : '' ?>>UNPAID</option>
                  </select>
              </div>
              <button type="submit" class="btn btn-primary col-12">Update Vehicle</button>
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
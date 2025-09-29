<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('dbconn.php');
date_default_timezone_set("Asia/Kolkata");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_name = $_POST['owner_name'];
    $vehicle_number = $_POST['vehicle_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $fuel_type = $_POST['fuel_type'];
    $status = "IN";
    $payment_status = $_POST['payment_status'];
    $in_time = date("Y-m-d H:i:s");

    $check = $conn->prepare("SELECT id FROM vehicles WHERE vehicle_number = ?");
    $check->bind_param("s", $vehicle_number);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
       
        $error = "⚠️ This vehicle number is already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO vehicles (owner_name, vehicle_number, vehicle_type, fuel_type, in_time, status, payment_status)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $owner_name, $vehicle_number, $vehicle_type, $fuel_type, $in_time, $status, $payment_status);

        if ($stmt->execute()) {
            $success = "✅ Vehicle entry successful!";
        } else {
            $error = "❌ Error while adding vehicle: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>    
    <div class="container mt-5">
        <a href="dashboard.php" class="btn btn-danger mb-3">← Back to Dashboard</a>
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body shadow">
                <h2 class="text-center text-primary">- Vehicle Entry -</h2>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label>Owner Name</label>
                        <input type="text" name="owner_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Vehicle Number</label>
                        <input type="text" name="vehicle_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Vehicle Type</label>
                        <select name="vehicle_type" class="form-control" required>
                            <option value="2 Wheeler">2 Wheeler</option>
                            <option value="4 Wheeler">4 Wheeler</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Fuel Type</label>
                        <select name="fuel_type" class="form-control" required>
                            <option value="Petrol">Petrol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Electric">Electric</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control" required>
                            <option value="Pending">Pending</option>
                            <option value="Paid">Paid</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary col-12">Add Vehicle</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Refresh page when back button is used to prevent duplicate form submission
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (performance.navigation.type === 2)) {
                window.location.reload(true);
            }
        });
    </script>
</body>
</html>

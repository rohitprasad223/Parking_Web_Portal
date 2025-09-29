<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0",false);
header("Pragma: no-Cache");

include 'dbconn.php';
$total_q = $conn->query("SELECT COUNT(*) AS total FROM vehicles");
$total = $total_q->fetch_assoc()['total'];

$in_q = $conn->query("SELECT COUNT(*) AS in_vehicles FROM vehicles WHERE status = 'IN'");
$in_count = $in_q->fetch_assoc()['in_vehicles'];

$out_q = $conn->query("SELECT COUNT(*) AS out_vehicles FROM vehicles WHERE status = 'OUT'");
$out_count = $out_q->fetch_assoc()['out_vehicles'];
?>


<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet"  href="assets/css/style.css">
  <style>
    body {
      background: lightgrey;
    }   
  </style>
</head>
<body>
      <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-home me-3" style="margin-top: 90px;"></i>Dashboard</a>
        <a href="vehicle-entry.php"><i class="fas fa-car me-3 mt-2"></i>Vehicle Entry</a>
        <a href="view-vehicle.php"><i class="fas fa-table me-3 mt-2"></i>View Vehicles</a>
      </div>
      <div class="header">
        <h5 class="mb-0"><i class="fas fa-car me-3 mt-2"></i>Parking Dashboard</h5>
        <div class="dropdown">
          <button class="btn btn-dark dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-2"></i>
            <?php echo $_SESSION['username']; ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item text-primary" href="profile.php">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-primary" href="change-password.php">Change Password</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
          </ul>
         </div>
        </div>
      </div>
      <div class="content">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card bg-primary text-white p-3 fw-bolder">
              <h5>Total Vehicles</h5>
              <h3><?php echo $total; ?></h3>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-success text-white p-3 fw-bolder">
              <h5>Vehicles IN</h5>
              <h3><?php echo $in_count; ?></h3>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-danger text-white p-3 fw-bolder">
              <h5>Vehicles OUT</h5>
              <h3><?php echo $out_count; ?></h3>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
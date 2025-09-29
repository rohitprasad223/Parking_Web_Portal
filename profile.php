<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'dbconn.php';

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($fullname, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .profile-card {
      max-width: 600px;
      margin: 50px auto;
      margin-top: 200px;
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
      <div class="card mx-auto mt-5" style="max-width: 600px;">
        <div class="card-body shadow">
          <h3 class="mb-4 text-primary text-center">-Profile-</h3>
          <table class="table mt-5 text-center ms-2">
            <tr>
              <th>Full Name :</th>
              <td><?php echo htmlspecialchars($fullname); ?></td>
            </tr>
            <tr>
              <th>Username :</th>
              <td><?php echo htmlspecialchars($username); ?></td>
            </tr>
            <tr>
              <th>Email :</th>
              <td><?php echo htmlspecialchars($email); ?></td>
            </tr>
          </table>
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
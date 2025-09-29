<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0",false);
header("Pragma: no-Cache");

include 'dbconn.php';

// --- Pagination Setup ---
$limit = 5; // per page records
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- Search Setup ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";

// --- Sort Setup ---
$validSort = ['id','owner_name','vehicle_number','vehicle_type','fuel_type','in_time','out_time','status','payment_status'];
$sortField = (isset($_GET['sort']) && in_array($_GET['sort'],$validSort)) ? $_GET['sort'] : 'id';
$sortOrder = (isset($_GET['order']) && $_GET['order']=='asc') ? 'ASC' : 'DESC';

// --- Query with Search, Sort, Pagination ---
$whereClause = $search ? "WHERE owner_name LIKE '%$search%' OR vehicle_number LIKE '%$search%' OR vehicle_type LIKE '%$search%'" : "";
$query = "SELECT * FROM vehicles $whereClause ORDER BY $sortField $sortOrder LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// total records for pagination
$countQuery = "SELECT COUNT(*) AS total FROM vehicles $whereClause";
$countRes = $conn->query($countQuery);
$totalRecords = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// helper for sort toggle
function sortLink($field,$label,$currentField,$currentOrder,$search){
    $order = ($currentField==$field && $currentOrder=='ASC') ? 'desc' : 'asc';
    $icon = '';
    if($currentField==$field){
        $icon = $currentOrder=='ASC' ? '▲' : '▼';
    }
    return "<a href='?sort=$field&order=$order&search=$search' class='text-white text-decoration-none'>$label $icon</a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Vehicles</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    th, td { vertical-align: middle; }
    .search-box { max-width: 300px; }
  </style>
</head>
<body>
<div class="container mt-4">
  <a href="dashboard.php" class="btn btn-danger mb-3">← Back to Dashboard</a>

  <div class="card shadow">
    <div class="card-body">
      <h3 class="text-center text-primary mb-4">- All Vehicle Records -</h3>

      <!-- Search -->
      <form method="GET" class="d-flex justify-content-end mb-3">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="🔍 Search vehicle..." class="form-control search-box me-2">
        <button type="submit" class="btn btn-primary me-2">Search</button>
      </form>

      <!-- Table -->
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-dark text-center">
            <tr>
              <th><?php echo sortLink('id','ID',$sortField,$sortOrder,$search); ?></th>
              <th><?php echo sortLink('owner_name','Owner Name',$sortField,$sortOrder,$search); ?></th>
              <th><?php echo sortLink('vehicle_number','Vehicle No.',$sortField,$sortOrder,$search); ?></th>
              <th><?php echo sortLink('vehicle_type','Type',$sortField,$sortOrder,$search); ?></th>
              <th><?php echo sortLink('fuel_type','Fuel',$sortField,$sortOrder,$search); ?></th>
              <th><?php echo sortLink('in_time','In Time',$sortField,$sortOrder,$search); ?></th>
              <th><?php echo sortLink('out_time','Out Time',$sortField,$sortOrder,$search); ?></th>
              <th><?php echo sortLink('status','Status',$sortField,$sortOrder,$search); ?></th>
              <th><?php echo sortLink('payment_status','Payment',$sortField,$sortOrder,$search); ?></th>
              <th>Update</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <?php if($result->num_rows > 0): ?>
              <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                <td><?php echo $row['vehicle_number']; ?></td>
                <td><?php echo $row['vehicle_type']; ?></td>
                <td><?php echo $row['fuel_type']; ?></td>
                <td><?php echo $row['in_time']; ?></td>
                <td><?php echo $row['out_time'] ?: '-'; ?></td>
                <td>
                  <?php if ($row['status'] === 'IN'): ?>
                    <span class="badge bg-success">IN</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">OUT</span>
                  <?php endif; ?>
                </td>
                <td><?php echo $row['payment_status']; ?></td>
                <td>
                  <div class="d-flex flex-column gap-1">
                    <?php if ($row['status'] === 'IN'): ?>
                      <a href="vehicle-exit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Exit</a>
                    <?php endif; ?>
                    <a href="edit-vehicle.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="10" class="text-center text-danger">No records found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav class="mt-3">
        <ul class="pagination justify-content-center">
          <?php if($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>&sort=<?php echo $sortField; ?>&order=<?php echo strtolower($sortOrder); ?>">Prev</a></li>
          <?php endif; ?>
          <?php for($i=1;$i<=$totalPages;$i++): ?>
            <li class="page-item <?php echo ($i==$page) ? 'active' : ''; ?>">
              <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&sort=<?php echo $sortField; ?>&order=<?php echo strtolower($sortOrder); ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>
          <?php if($page < $totalPages): ?>
            <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>&sort=<?php echo $sortField; ?>&order=<?php echo strtolower($sortOrder); ?>">Next</a></li>
          <?php endif; ?>
        </ul>
      </nav>

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

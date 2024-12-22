<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
include '../includes/db.php';

// Fetch all users or search users
$search_query = "";
$role_filter = "all";
$users_query = "SELECT id, username, role FROM users WHERE 1=1";

if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $users_query .= " AND username LIKE '%$search_query%'";
}

if (isset($_POST['filter_role'])) {
    $role_filter = $_POST['filter_role'];
    if ($role_filter != 'all') {
        $users_query .= " AND role = '$role_filter'";
    }
}

$users_result = $conn->query($users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light p-3 mb-4 shadow-sm">
         <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav">
                    <li class="nav-item">
                         <a class="nav-link" href="index.php">Customer Dashboard</a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link active" href="admin_dashboard.php">Admin Dashboard</a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link active" href="user.php">User List</a>
                    </li>
              </ul>
         </div>

         <!-- Mobile Toggle Button for Navbar -->
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
         </button>

         <!-- Welcome Message for Logged-in Users -->
         <div class="dropdown ms-4 me-3">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!
              </a>
              <ul class="dropdown-menu" aria-labelledby="userDropdown">
                    <?php if (isset($_SESSION['username'])) { ?>
                         <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                         <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    <?php } ?>
              </ul>
         </div>
    </nav>
    
    <div class="container mt-5">
        <h1 class="text-center text-primary mb-4">All Users</h1>

      
        <div class="mb-4 text-center">

            <div>
                <div class="alert alert-info" role="alert">  Total Users: <?php echo $users_result->num_rows; ?>
            </div>

            <div class="d-flex justify-content-center mb-2">
                <form action="user.php" method="POST" class="d-flex justify-content-center">
                    <input type="text" name="search_query" class="form-control w-10" placeholder="Search by username" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" name="search" class="btn btn-primary ms-2">Search</button>
                </form>

                <form action="user.php" method="POST" class="d-inline-block mt-2">
                    <select name="filter_role" class="form-select w-auto d-inline-block">
                        <option value="all" <?php echo $role_filter == 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="customer" <?php echo $role_filter == 'customer' ? 'selected' : ''; ?>>Customer</option>
                        <option value="admin" <?php echo $role_filter == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <button type="submit" class="btn btn-primary ms-2">Filter</button>
                </form>

        </div>

      

        <!-- Table -->
        <div class="bg-light p-4 rounded shadow-sm">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td>
                                <form action="settings.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_user_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_user" value="1" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS (optional for modal, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

 <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
         header('Location: login.php');
         exit;
    }

    include '../includes/db.php';

    // Handle the item insertion process
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
         $name = $_POST['name'];
         $description = $_POST['description'];
         $price = $_POST['price'];
         $quantity = $_POST['quantity'];
         $image = $_FILES['image']['name'];
         $target_dir = "../uploads/";
         $target_file = $target_dir . basename($image);

         // Ensure the target directory exists
         if (!is_dir($target_dir)) {
              mkdir($target_dir, 0777, true);
         }

         // Move the uploaded file to the target directory
         if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
              $query = "INSERT INTO items (name, description, price, quantity, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
              $stmt = $conn->prepare($query);
              $stmt->bind_param("ssdis", $name, $description, $price, $quantity, $image);

              if ($stmt->execute()) {
                    header('Location: admin_dashboard.php');
                    exit;
              } else {
                    $error = "Error adding item: " . $conn->error;
              }
         } else {
              $error = "Error uploading image.";
         }
    }

    // Handle the item deletion process
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
         $item_id_to_delete = $_POST['item_id_to_delete'];

         $query = "DELETE FROM items WHERE id = ?";
         $stmt = $conn->prepare($query);
         $stmt->bind_param("i", $item_id_to_delete);

         if ($stmt->execute()) {
              header('Location: admin_dashboard.php');
              exit;
         } else {
              $error = "Error deleting item: " . $conn->error;
         }
    }

    // Handle the search functionality
    if (isset($_GET['search'])) {
         $search = $_GET['search'];
         $query = "SELECT * FROM items WHERE name LIKE ? OR description LIKE ?";
         $stmt = $conn->prepare($query);
         $search_param = "%" . $search . "%";
         $stmt->bind_param("ss", $search_param, $search_param);
         $stmt->execute();
         $result = $stmt->get_result();
    } else {
         // Fetch all items for display
         $query = "SELECT * FROM items";
         $result = $conn->query($query);
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <title>Admin Dashboard</title>
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

    <div class="container">
         <h2 class="my-4">Welcome to the Admin Dashboard</h2>
         <p class="lead">Manage your store items efficiently and effectively.</p>
         <p>Welcome, <?php echo ucfirst($_SESSION['username']); ?>!</p>

         <div class="d-flex justify-content-between align-items-center mb-3">
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">Add Item</button>

              <?php
                    $query = "SELECT COUNT(*) AS total_items FROM items";
                    $count_result = $conn->query($query);
                    $total_items = $count_result->fetch_assoc()['total_items'];
              ?>
              
              <p>Total items in store: <?php echo $total_items; ?></p>

              <form method="GET" action="admin_dashboard.php" class="d-flex">
                    <input type="text" class="form-control me-2" name="search" placeholder="Search items..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
              </form>
         </div>

         <!-- Display all store items -->
         <h3>Store Items</h3>
         <table class="table">
              <thead>
                    <tr>
                         <th>ID</th>
                         <th>Name</th>
                         <th>Description</th>
                         <th>Price</th>
                         <th>Quantity</th>
                         <th>Image</th>
                         <th>Date Added</th>
                         <th>Actions</th>
                    </tr>
              </thead>
              <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                         <tr>
                              <td><?php echo $row['id']; ?></td>
                              <td><?php echo $row['name']; ?></td>
                              <td><?php echo $row['description']; ?></td>
                              <td>$<?php echo $row['price']; ?></td>
                              <td><?php echo $row['quantity']; ?></td>
                              <td><img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="50"></td>
                              <td><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></td>
                              <td>
                                    <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setItemToDelete(<?php echo $row['id']; ?>)">Delete</button>
                              </td>
                         </tr>
                    <?php } ?>
              </tbody>
         </table>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
         <div class="modal-dialog">
              <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                         <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                         <form method="POST" enctype="multipart/form-data">
                              <div class="mb-3">
                                    <label for="name" class="form-label">Item Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                              </div>
                              <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" required></textarea>
                              </div>
                              <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="text" class="form-control" id="price" name="price" required>
                              </div>
                              <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                              </div>
                              <div class="mb-3">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="image" name="image" required>
                              </div>
                              <button type="submit" name="add_item" class="btn btn-primary">Add Item</button>
                         </form>
                    </div>
              </div>
         </div>
    </div>

    <!-- Delete Item Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
         <div class="modal-dialog">
              <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                         Are you sure you want to delete this item?
                    </div>
                    <div class="modal-footer">
                         <form method="POST">
                              <input type="hidden" name="item_id_to_delete" id="itemIdToDelete">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, delete</button>
                         </form>
                    </div>
              </div>
         </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
         // Function to set the item ID to be deleted in the hidden input field
         function setItemToDelete(itemId) {
              document.getElementById('itemIdToDelete').value = itemId;
         }
    </script>
    </body>
    </html>
    ```
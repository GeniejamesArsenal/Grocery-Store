<?php  
session_start();  

include '../includes/db.php';  

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Fetch items from the database based on the search query
$query = "SELECT * FROM items WHERE name LIKE ?";
$stmt = $conn->prepare($query);
$searchTerm = '%' . $searchQuery . '%';
$stmt->bind_param('s', $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Grocery Store</title>  
    <link rel="stylesheet" href="../assets/css/item.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>  
<style>
    .kin-item {
        max-width: 250px;
        max-height: 400px;
        font-size: 0.9rem; /* Adjust font size to fit within the kin-item */
    }
    .item-card {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        transition: transform 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .item-card:hover {
        transform: translateY(-5px);
    }

    .item-card img {
        height: 200px;
        object-fit: cover;
    }

    .item-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .item-card .card-text {
        text-align: justify;
        font-size: 0.9rem;
    }
  
</style>
<body>  
<nav class="navbar navbar-expand-lg navbar-light bg-light p-3 mb-4 shadow-sm">
    <a class="navbar-brand" href="index.php">Grocery System</a>
    
    <!-- Mobile Toggle Button for Navbar -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Content -->
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="view_cart.php">
                    <i class="fas fa-shopping-cart"></i> Cart
                </a>
            </li>
        </ul>
    </div>

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

  
    <form method="GET" action="index.php" class="d-flex ms-auto mb-0">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search items" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

</nav>

<div class="container">  
    <?php
    // Initialize cart count
    $cartCount = 0;

    // Check if the cart session variable exists and count the items
    if (isset($_SESSION['cart'])) {
        $cartCount = count($_SESSION['cart']);
    }
    ?>

    
    <h3>Available Items</h3>  

    <div class="row">  
        <?php while ($row = $result->fetch_assoc()) {   
            $imagePath = '../uploads/' . $row['image']; // Path to the uploaded image  
            ?>  
            <div class="col-md-4 mb-4 kin-item">  

                <div class="card item-card">  
                    <!-- Display item image -->  
                    <?php if (file_exists($imagePath)) { ?>  
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">  
                    <?php } else { ?>  
                        <img src="default-image.jpg" class="card-img-top" alt="Default Image">  
                        <p class="text-danger">Image not found: <?php echo htmlspecialchars($imagePath); ?></p> <!-- Debug message -->  
                    <?php } ?>  
                    <div class="card-body">  
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>  
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>  
                        <p class="card-text ">$<?php echo htmlspecialchars($row['price']); ?></p>  
                        <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn btn-success">Add to Cart</a>  
                    </div>  
                </div>  

            </div>  
        <?php } ?>  
    </div>  
</div>  

<script>
 document.addEventListener('DOMContentLoaded', function() {
    var cartCountElement = document.querySelector('.fas.fa-shopping-cart');
    if (cartCountElement) {
        cartCountElement.innerHTML = ' ' + <?php echo $cartCount; ?> + ' ';
    }
});
</script>

<script src="../assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>
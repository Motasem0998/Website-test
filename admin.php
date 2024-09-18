<?php
// Start the session to track user login status
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "ots";  // Your username
$password = "ots";  // Your password
$dbname = "ecommerce";  // Ensure the database name is correct

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding a new section
if (isset($_POST['addSection'])) {
    $sectionName = isset($_POST['section_name']) ? $conn->real_escape_string($_POST['section_name']) : '';
    if (!empty($sectionName)) {
        $conn->query("INSERT INTO sections (name) VALUES ('$sectionName')");
        echo "New section added successfully!";
    } else {
        echo "Please provide a section name.";
    }
}

// Handle section edit
if (isset($_POST['editSection'])) {
    $sectionId = intval($_POST['section_id']);
    $sectionName = isset($_POST['section_name']) ? $conn->real_escape_string($_POST['section_name']) : '';
    if (!empty($sectionName)) {
        $conn->query("UPDATE sections SET name='$sectionName' WHERE id=$sectionId");
        echo "Section updated successfully!";
    } else {
        echo "Please provide a valid section name.";
    }
}

// Handle section deletion
if (isset($_GET['deleteSection']) && !empty($_GET['deleteSection'])) {
    $sectionId = intval($_GET['deleteSection']);
    // Remove all products linked to this section or set their section to null, depending on your requirement
    $conn->query("UPDATE products SET section_id = NULL WHERE section_id=$sectionId");
    $conn->query("DELETE FROM sections WHERE id=$sectionId");
    echo "Section deleted successfully!";
}

// Fetch all sections for display in the admin panel
$sectionsResult = $conn->query("SELECT * FROM sections");
$sections = $sectionsResult->fetch_all(MYSQLI_ASSOC);

// Add or Update product
if (isset($_POST['addProduct'])) {
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $section_id = isset($_POST['section']) ? intval($_POST['section']) : 0;
    $image = isset($_POST['image']) ? $conn->real_escape_string($_POST['image']) : '';
    $link = isset($_POST['link']) ? $conn->real_escape_string($_POST['link']) : '';

    if (!empty($name) && !empty($price) && !empty($description) && !empty($rating) && !empty($section_id) && !empty($image) && !empty($link)) {
        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            // Update existing product
            $product_id = intval($_POST['product_id']);
            $sql = "UPDATE products SET name='$name', price=$price, description='$description', rating=$rating, section_id=$section_id, image='$image', link='$link' WHERE id=$product_id";
            $conn->query($sql);
            echo "Product updated successfully!";
        } else {
            // Insert new product
            $sql = "INSERT INTO products (name, price, description, rating, section_id, image, link) 
                    VALUES ('$name', $price, '$description', $rating, $section_id, '$image', '$link')";
            $conn->query($sql);
            echo "New product added successfully!";
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

// Handle product deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$delete_id");
    echo "Product deleted successfully!";
}

// Fetch all products for display in the admin panel
$productsResult = $conn->query("SELECT products.*, sections.name AS section_name FROM products LEFT JOIN sections ON products.section_id = sections.id");
$products = $productsResult->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin-style.css"> <!-- Ensure this file exists -->
</head>
<body>

    <h1>Admin Panel</h1>

    <!-- Add New Section Form -->
    <h2>Add New Section</h2>
    <form method="POST" action="admin.php">
        <label for="section_name">Section Name:</label>
        <input type="text" name="section_name" id="section_name" required>
        <button type="submit" name="addSection">Add Section</button>
    </form>

    <!-- Display Sections with Edit and Delete Options -->
    <h2>Manage Sections</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Section ID</th>
                <th>Section Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sections as $section): ?>
                <tr>
                    <td><?php echo $section['id']; ?></td>
                    <td><?php echo htmlspecialchars($section['name']); ?></td>
                    <td>
                        <form method="POST" action="admin.php" style="display:inline;">
                            <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                            <input type="text" name="section_name" value="<?php echo htmlspecialchars($section['name']); ?>" required>
                            <button type="submit" name="editSection">Edit</button>
                        </form>
                        <a href="admin.php?deleteSection=<?php echo $section['id']; ?>" onclick="return confirm('Are you sure you want to delete this section?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Add or Edit Product Form -->
    <h2>Add or Edit Product</h2>
    <form method="POST" action="admin.php">
        <input type="hidden" name="product_id" id="product_id">

        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="section">Section:</label>
        <select name="section" id="section" required>
            <?php foreach ($sections as $section): ?>
                <option value="<?php echo $section['id']; ?>"><?php echo htmlspecialchars($section['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="rating">Star Rating (1-5):</label>
        <select name="rating" id="rating" required>
            <option value="1">1 Star</option>
            <option value="2">2 Stars</option>
            <option value="3">3 Stars</option>
            <option value="4">4 Stars</option>
            <option value="5">5 Stars</option>
        </select>

        <label for="image">Image URL:</label>
        <input type="text" name="image" id="image" required>

        <label for="link">Product Link:</label>
        <input type="url" name="link" id="link" required>

        <button type="submit" name="addProduct">Save Product</button>
    </form>

    <!-- Display Products in Admin Panel -->
    <h2>Existing Products</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Rating</th>
                <th>Section</th>
                <th>Image</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($product['description'] ?? ''); ?></td>
                    <td><?php echo "$" . number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['rating']; ?> Stars</td>
                    <td><?php echo htmlspecialchars($product['section_name'] ?? ''); ?></td>
                    <td><img src="<?php echo htmlspecialchars($product['image'] ?? ''); ?>" alt="Product Image" width="100"></td>
                    <td><a href="<?php echo htmlspecialchars($product['link'] ?? ''); ?>" target="_blank">Visit Product</a></td>
                    <td>
                        <button onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo htmlspecialchars($product['description']); ?>', <?php echo $product['section_id']; ?>, <?php echo $product['rating']; ?>, '<?php echo htmlspecialchars($product['image']); ?>', '<?php echo htmlspecialchars($product['link']); ?>')">Edit</button>
                        <a href="admin.php?delete=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    function editProduct(id, name, price, description, section, rating, image, link) {
        document.getElementById('product_id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('price').value = price;
        document.getElementById('description').value = description;
        document.getElementById('section').value = section;
        document.getElementById('rating').value = rating;
        document.getElementById('image').value = image;
        document.getElementById('link').value = link;
    }
    </script>

</body>
</html>


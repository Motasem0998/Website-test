<?php
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

// Fetch all available sections
$sectionsSql = "SELECT * FROM sections";
$sectionsResult = $conn->query($sectionsSql);

// Fetch all products and their respective sections and ratings from the database
$productsSql = "SELECT products.*, sections.name AS section_name FROM products 
                LEFT JOIN sections ON products.section_id = sections.id";
$productsResult = $conn->query($productsSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-commerce website</title>

    <!-- Link external stylesheet -->
    <link rel="stylesheet" href="style.css">

    <!-- Font Awesome for stars -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />

    <!-- Additional Inline Style -->
    <style>
        /* Product filter and container styling */
	
/* Container styling for the section filter */
.filter-container {
    margin: 30px auto;
    text-align: center;
    font-family: 'Roboto', sans-serif;
}

/* Style for the label of the section filter */
.filter-container label {
    font-weight: bold;
    font-size: 18px;
    color: #333;
    margin-right: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Main styling for the dropdown select */
.filter-container select {
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 30px;  /* Round corners for a more modern look */
    border: 2px solid #4CAF50;
    background: linear-gradient(145deg, #f0f0f0, #e6e6e6);  /* Gradient background */
    color: #333;
    outline: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.6);  /* Soft outer shadow and inner highlight */
}

/* Dropdown hover effect */
.filter-container select:hover {
    background: linear-gradient(145deg, #e6e6e6, #f0f0f0);  /* Reverse gradient on hover */
    cursor: pointer;
    border-color: #45a049;  /* Change border color on hover */
}

/* Focus effect for the dropdown */
.filter-container select:focus {
    background: linear-gradient(145deg, #e6e6e6, #f0f0f0);  /* Subtle background change */
    border-color: #4CAF50;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3), inset 0 2px 4px rgba(255, 255, 255, 0.8);  /* Enhanced shadow on focus */
}

/* Animation when dropdown is opened */
.filter-container select:focus-within {
    animation: dropdownAnimation 0.4s ease forwards;
}

/* Keyframes for smooth opening animation */
@keyframes dropdownAnimation {
    0% {
        transform: scale(0.9);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Option dropdown customization */
.filter-container select option {
    padding: 10px;
    font-size: 14px;
    background-color: #fff;
    color: #333;
}

/* Custom scrollbar for the dropdown in modern browsers */
.filter-container select::-webkit-scrollbar {
    width: 8px;
}

.filter-container select::-webkit-scrollbar-thumb {
    background-color: #4CAF50;
    border-radius: 10px;
}

/* Dropdown disabled option style */
.filter-container select:disabled {
    background-color: #ddd;
    cursor: not-allowed;
}

/* Smooth transition for dropdown entries */
.filter-container select option {
    transition: background-color 0.3s ease;
}

.filter-container select option:hover {
    background-color: #4CAF50;
    color: #fff;
}




        .pro-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
        }

        .pro {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            width: 250px;
            height: 420px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pro img {
            max-width: 100%;
            max-height: 220px;
            object-fit: cover;
        }

        .pro:hover {
            transform: scale(1.05);
            box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.2);
        }

        .star {
            display: inline-block;
            margin-top: 5px;
        }

        .star .fas {
            color: #FFD700;
            font-size: 16px;
        }

        .star .far {
            color: #ddd;
            font-size: 16px;
        }

        .section-name {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            text-transform: uppercase;
            background-color: #f9f9f9;
            padding: 5px;
            border-radius: 5px;
        }

        .pro a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .pro a:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <section id="header">
        <a href="#"><img src="images/logo.png" class="logo" alt=""></a>
        <div>
            <ul id="navbar">
                <li><a class="active" href="index.php">Home</a></li>
                <li><a href="shop.html">Shop</a></li>
                <li><a href="blog.html">Blog</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li id="lg-bag"><a href="cart.html"><i class="far fa-shopping-bag"></i></a></li>
                <a href="#" id="close"><i class="far fa-times"></i></a>
            </ul>
        </div>
        <div id="mobile">
            <a href="cart.html"><i class="far fa-shopping-bag"></i></a>
            <i id="bar" class="fas fa-outdent"></i>
        </div>
    </section>

    <section id="hero">
        <h4>Trade-in-offer</h4>
        <h2>Super value deals</h2>
        <h1>On all products</h1>
        <p>Save more with coupons & up to 70% off!</p>
        <button>Shop Now</button>
    </section>

    <!-- Filter by Section -->
    <div class="filter-container">
        <label for="sectionFilter">Filter by Section:</label>
        <select id="sectionFilter">
            <option value="all">All Sections</option>
            <?php
            if ($sectionsResult->num_rows > 0) {
                while ($section = $sectionsResult->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($section['name']) . "'>" . htmlspecialchars($section['name']) . "</option>";
                }
            }
            ?>
        </select>
    </div>

    <!-- Dynamic Product Section -->
    <section id="product1" class="section-p1">
        <h2>Featured Products</h2>
        <p>Summer Collection New Modern Design</p>
        <div class="pro-container" id="productContainer">
            <?php
            // Check if any products exist in the database
            if ($productsResult->num_rows > 0) {
                // Fetch and display products from the database
                while ($row = $productsResult->fetch_assoc()) {
                    $productLink = !empty($row['link']) ? htmlspecialchars($row['link']) : '#'; // Ensure valid product link

                    echo "<a href='$productLink' target='_blank' class='product-card' data-section='" . htmlspecialchars($row['section_name']) . "'>";
                    echo "<div class='pro'>";
                    echo "<span class='section-name'>" . htmlspecialchars($row['section_name']) . "</span>"; // Display product section
                    echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
                    echo "<div class='des'>";
                    echo "<h5>" . htmlspecialchars($row['name']) . "</h5>";

                    // Display star rating
                    echo "<div class='star'>";
                    $rating = isset($row['rating']) ? (int)$row['rating'] : 0;  // Default to 0 if 'rating' is not set
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo "<i class='fas fa-star'></i>"; // Filled star
                        } else {
                            echo "<i class='far fa-star'></i>"; // Empty star
                        }
                    }
                    echo "</div>";
                    
                    echo "<h4>$" . number_format($row['price'], 2) . "</h4>";
                    echo "</div>";
                    echo "</div>";
                    echo "</a>";
                }
            } else {
                echo "<p>No products available at the moment.</p>";
            }
            ?>
        </div>
    </section>

    <footer class="section-p1">
        <div class="col">
            <img class="logo" src="images/logo.png" alt="">
            <h4>Contact</h4>
            <p><strong>Address:</strong> Lahore, Pakistan - 54840</p>
            <p><strong>Phone:</strong> +92-321-4655990</p>
            <p><strong>Hours:</strong> 10:00 - 18:00, Mon - Sat</p>
        </div>
        <div class="col">
            <h4>About</h4>
            <a href="#">About us</a>
            <a href="#">Delivery Information</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
            <a href="#">Contact Us</a>
        </div>
        <div class="col">
            <h4>My Account</h4>
            <a href="#">Sign In</a>
            <a href="#">View Cart</a>
            <a href="#">My Wishlist</a>
            <a href="#">Track My Order</a>
            <a href="#">Help</a>
        </div>
        <div class="col install">
            <h4>Install App</h4>
            <p>From App Store or Google Play</p>
            <div class="row">
                <img src="images/pay/app.jpg" alt="">
                <img src="images/pay/play.jpg" alt="">
            </div>
            <p>Secured Payment Gateway</p>
            <img src="images/pay/pay.png" alt="">
        </div>
        <div class="copyright">
            <p>Created By Muhammad Awais | All Rights Reserved | &#169; 2023</p>
        </div>
    </footer>

    <!-- JS and other scripts -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Script to filter products by section -->
    <script>
        document.getElementById('sectionFilter').addEventListener('change', function() {
            var selectedSection = this.value;
            var products = document.querySelectorAll('.product-card');

            products.forEach(function(product) {
                if (selectedSection === 'all' || product.dataset.section === selectedSection) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    </script>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>


<?php
    @include 'connection.php';

    // Get productId from URL and sanitize it
    $productId = isset($_GET['productId']) ? intval($_GET['productId']) : 0;

    // Prepare the SQL query to fetch product details
    $stmt = $conn->prepare("SELECT * FROM product WHERE productId = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch the product data
    $product = $result->fetch_assoc();
    $artisanID = $product['artisanId']; // Assuming artisanID exists in product table
    
    // Close the statement
    $stmt->close();

    if ($product) {
        $stmt = $conn->prepare("SELECT * FROM artisanprofile WHERE artisanID = ?");
        $stmt->bind_param("i", $artisanID);
        $stmt->execute();
        $artisanResult = $stmt->get_result();
        $artisan = $artisanResult->fetch_assoc();

        // Close the statement
        $stmt->close();
    }
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Product Content</title>
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative&family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="css/product_details.css">
</head>
<body>

<section>
    <div id="1"></div>
    <script>
        load("header.html");
        function load(url) {
            req = new XMLHttpRequest();
            req.open("GET", url, false);
            req.send(null);
            document.getElementById(1).innerHTML = req.responseText;
        }
    </script>

    <script src="js/header.js"></script>

    <div class="parent">
        <?php if ($product): ?>
        <div class="div2">
            <img src="../artisan-main/uploaded_img/<?php echo htmlspecialchars($product['productImage']); ?>">
            <h2><?php echo htmlspecialchars($product['nameOfProduct']); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
            <p><strong>Origin:</strong> <?php echo htmlspecialchars($artisan['address']); ?></p>
            <p><strong>Artisan:</strong> <?php echo htmlspecialchars($artisan['name']); ?></p>
        </div>

        <div class="div3">
            <p><?php echo nl2br(htmlspecialchars($product['productDescription'])); ?></p>
        </div>
        <?php else: ?>
        <p>Product not found.</p>
        <?php endif; ?>
    </div>

    <div class="style">
        <br>
        <div class="edi">
        </div>
    </div>
    
    <?php
        @include 'connection.php';

        // Get productId from URL and sanitize it
        $productId = isset($_GET['productId']) ? intval($_GET['productId']) : 0;

        // Fetch reviews with guest names and profile images
        $stmt = $conn->prepare("SELECT rp.reviewMessage, gp.name, gp.profile_image 
                                FROM reviewproduct rp 
                                JOIN guestprofile gp ON rp.guestId = gp.guestId 
                                WHERE rp.productId = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $reviews = $stmt->get_result();
        $stmt->close();
    ?>

    <div class="review-container">
            <div class="box1">
                <br>
                <h1>Reviews</h1>

                <?php if ($reviews->num_rows > 0): ?>
                    <?php while ($review = $reviews->fetch_assoc()): ?>
                        <div class="nagReview">
                            <div class="logoPic">
                                <img src="../guest-main/profile_img/<?php echo htmlspecialchars($review['profile_image']); ?>" alt="User Avatar">
                            </div>

                            <div class="allInfo">
                                <h6><?php echo htmlspecialchars($review['name']); ?></h6>
                                <p><?php echo nl2br(htmlspecialchars($review['reviewMessage'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="cute">No reviews yet.</p>
                <?php endif; ?>
            </div>

        </div>

    <!-- FOOTER -->
    <div id="4"></div>
    <script>
        load("footer.html");
        function load(url) {
            req = new XMLHttpRequest();
            req.open("GET", url, false);
            req.send(null);
            document.getElementById(4).innerHTML = req.responseText;
        }
    </script>
</body>
</html>

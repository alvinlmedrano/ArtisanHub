<?php
    @include 'connection.php';

    // Get storyId from URL and sanitize it
    $storyId = isset($_GET['storyId']) ? intval($_GET['storyId']) : 0;

    // Prepare the SQL query to fetch story details
    $stmt = $conn->prepare("SELECT * FROM artisanstories WHERE storyId = ?");
    $stmt->bind_param("i", $storyId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the story data
    $story = $result->fetch_assoc();
    $artisanID = $story['artisanId'] ?? 0;

    // Close the statement
    $stmt->close();

    // Fetch artisan details if story exists
    if ($story) {
        $stmt = $conn->prepare("SELECT * FROM artisanprofile WHERE artisanID = ?");
        $stmt->bind_param("i", $artisanID);
        $stmt->execute();
        $artisanResult = $stmt->get_result();
        $artisan = $artisanResult->fetch_assoc();
        $stmt->close();
    }

    $select = mysqli_query($conn, "SELECT * FROM product WHERE artisanID = $artisanID");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Story Details</title>
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative&family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="css/stories-deets.css">
</head>
<body>

<section>
    <div id="1"></div>
    <script>
        function load(url, elementId) {
            const req = new XMLHttpRequest();
            req.open("GET", url, false);
            req.send(null);
            document.getElementById(elementId).innerHTML = req.responseText;
        }
        load("header.html", 1);
    </script>

    <div class="parent">
        <?php if ($story): ?>
        


        <div class="div3">
            <h6 class="artisan-name"><strong><?php echo htmlspecialchars($artisan['name'] ?? 'Unknown Artisan'); ?></strong></h6>
            <p class="cuteko"><?php echo nl2br(htmlspecialchars($story['story'])); ?></p>
        </div>
        <?php else: ?>
        <p>Story not found.</p>
        <?php endif; ?>

        <div class="div2">
            <img src="artisan-main/artisan_image/<?php echo htmlspecialchars($story['artisanPhoto']); ?>" alt="Artisan Photo">
            
            <p><strong>Category:</strong> <?php echo htmlspecialchars($story['category']); ?></p>
            <p><strong>Place:</strong> <?php echo htmlspecialchars($story['place']); ?></p>
        </div>

    </div>
    
    <div class="button-container">
         <p class="crafts-button">CRAFTS</p>
    </div>
    
    <div>
        <div class="small-container">
            <div class="row">
                <?php
                if (mysqli_num_rows($select) > 0) {
                    while ($product = mysqli_fetch_assoc($select)) {
                ?>
                <div class="container">

                    <div class="card">
                        <div class="img-container">
                            <img src="artisan-main/uploaded_img/<?php echo $product['productImage']; ?>" alt="<?php echo $product['nameOfProduct']; ?>">
                        </div>
                        <h5 class="product_name"><?php echo $product['nameOfProduct']; ?></h5>
                    </div>
                </div>
                <?php 
                    }
                }
                ?>
            </div>
        </div>
    </div>
    
    <br><br><br><br><br>
    <!-- FOOTER -->
    <div id="4"></div>
    <script>
        load("footer.html", 4);
    </script>
</section>

</body>
</html>
<?php
@include 'connection.php';
$select = mysqli_query($conn, "SELECT * FROM artisanstories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARTISAN STORIES</title>
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative&family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="css/stories.css">
</head>
<body>

<section>
    <div id="1"></div>
    <script>
        load("header.html");
        function load(url) {
            const req = new XMLHttpRequest();
            req.open("GET", url, false);
            req.send(null);
            document.getElementById(1).innerHTML = req.responseText;
        }
    </script>

    <script src="js/header.js"></script>

    <div class="small-container">
        <div class="row">
            <?php
            if (mysqli_num_rows($select) > 0) {
                while ($story = mysqli_fetch_assoc($select)) {
            ?>
            <div class="container" onclick="window.location.href='stories-deets.php?storyId=<?php echo $story['storyId']; ?>'">
                <div class="card">
                    <div class="img-container">
                        <img src="artisan_image/<?php echo $story['artisanPhoto']; ?>" alt="<?php echo $story['title']; ?>">
                    </div>
                    <h5 class="product_name"><?php echo $story['title']; ?></h5>
                </div>
            </div>
            <?php 
                }
            }
            ?>
        </div>
    </div>

    <br><br><br>

    <!-- FOOTER -->
    <div id="4"></div>
    <script>
        load("footer.html");
        function load(url) {
            const req = new XMLHttpRequest();
            req.open("GET", url, false);
            req.send(null);
            document.getElementById(4).innerHTML = req.responseText;
        }
    </script>
</section>
</body>
</html>

<?php
@include 'connection.php';

if (isset($_GET['productId'])) {
    $productId = intval($_GET['productId']);
    
    $query = "SELECT * FROM product WHERE productId = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($product = mysqli_fetch_assoc($result)) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>

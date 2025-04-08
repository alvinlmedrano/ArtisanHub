<?php
// Destroy all session data
session_unset();
session_destroy();

// Prevent caching by the browser
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");  // For HTTP/1.0 compatibility
header("Expires: 0");         // Ensure the page is not cached

// Redirect after logout
header("Location: ../login.php");
exit();
?>

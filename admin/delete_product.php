<?php
require_once 'auth.php';
require_once '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Get all image filenames to delete them from the server
    $sql_select = "SELECT image FROM product_images WHERE product_id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        while($image = $result->fetch_assoc()) {
            if (!empty($image['image'])) {
                $image_path = UPLOAD_PATH . $image['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        $stmt_select->close();
    }

    // Delete the product from the database
    $sql_delete = "DELETE FROM products WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id);

        if ($stmt_delete->execute()) {
            header("Location: list_products.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt_delete->close();
    }
} else {
    header("Location: list_products.php");
    exit();
}
?>

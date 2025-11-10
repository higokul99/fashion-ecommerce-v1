<?php
require_once 'auth.php';
require_once '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // First, get the image filename to delete it from the server
    $sql_select = "SELECT image FROM categories WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($result->num_rows == 1) {
            $category = $result->fetch_assoc();
            if (!empty($category['image'])) {
                $image_path = UPLOAD_PATH . $category['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        $stmt_select->close();
    }

    // Now, delete the category from the database
    $sql_delete = "DELETE FROM categories WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id);

        if ($stmt_delete->execute()) {
            // Redirect to the category list page
            header("Location: list_categories.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt_delete->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    // Redirect if no ID is provided
    header("Location: list_categories.php");
    exit();
}
?>

<?php
require_once 'auth.php';
require_once '../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Get the image filename to delete it from the server
    $sql_select = "SELECT banner_image FROM banners WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($result->num_rows == 1) {
            $banner = $result->fetch_assoc();
            if (!empty($banner['banner_image'])) {
                $image_path = UPLOAD_PATH . $banner['banner_image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        $stmt_select->close();
    }

    // Delete the banner from the database
    $sql_delete = "DELETE FROM banners WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id);

        if ($stmt_delete->execute()) {
            header("Location: banners.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt_delete->close();
    }
} else {
    header("Location: banners.php");
    exit();
}
?>

<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

$message = '';
$banner = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM banners WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $banner = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $cta_text = trim($_POST['cta_text']);
    $cta_link = trim($_POST['cta_link']);
    $status = isset($_POST['status']) ? 1 : 0;
    $banner_image = $_POST['current_image'];

    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        // Delete old image
        if(!empty($banner_image) && file_exists(UPLOAD_PATH . $banner_image)) {
            unlink(UPLOAD_PATH . $banner_image);
        }

        $target_dir = UPLOAD_PATH;
        $image_name = basename($_FILES["banner_image"]["name"]);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $banner_image = uniqid() . '.' . $image_ext;
        $target_file = $target_dir . $banner_image;
        if (!move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
            $message = "Error uploading file.";
        }
    }

    if (empty($message)) {
        $sql = "UPDATE banners SET title = ?, cta_text = ?, cta_link = ?, status = ?, banner_image = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssisi", $title, $cta_text, $cta_link, $status, $banner_image, $id);
            if ($stmt->execute()) {
                header("Location: banners.php");
                exit;
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

if (!$banner) {
    header("Location: banners.php");
    exit();
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Edit Banner</h2>
    <a href="banners.php" class="text-blue-500 hover:text-blue-700">Back to Banners</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <form action="edit_banner.php?id=<?php echo $banner['id']; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
        <input type="hidden" name="current_image" value="<?php echo $banner['banner_image']; ?>">
        <!-- Form fields -->
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" name="title" id="title" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($banner['title']); ?>">
        </div>
        <div class="mb-4">
            <label for="cta_text" class="block text-gray-700 font-bold mb-2">CTA Text</label>
            <input type="text" name="cta_text" id="cta_text" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($banner['cta_text']); ?>">
        </div>
        <div class="mb-4">
            <label for="cta_link" class="block text-gray-700 font-bold mb-2">CTA Link</label>
            <input type="text" name="cta_link" id="cta_link" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($banner['cta_link']); ?>">
        </div>
        <div class="mb-4">
            <label for="banner_image" class="block text-gray-700 font-bold mb-2">Banner Image</label>
            <input type="file" name="banner_image" id="banner_image" class="w-full px-3 py-2 border rounded-lg">
            <img src="../uploads/<?php echo htmlspecialchars($banner['banner_image']); ?>" class="h-20 mt-2">
        </div>
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="status" value="1" class="form-checkbox" <?php if ($banner['status']) echo 'checked'; ?>>
                <span class="ml-2">Active</span>
            </label>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Banner</button>
        </div>
    </form>
</main>
<?php require_once 'includes/footer.php'; ?>

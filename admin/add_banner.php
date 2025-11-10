<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $cta_text = trim($_POST['cta_text']);
    $cta_link = trim($_POST['cta_link']);
    $status = isset($_POST['status']) ? 1 : 0;
    $banner_image = '';

    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $target_dir = UPLOAD_PATH;
        $image_name = basename($_FILES["banner_image"]["name"]);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $banner_image = uniqid() . '.' . $image_ext;
        $target_file = $target_dir . $banner_image;
        if (!move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
            $message = "Sorry, there was an error uploading your file.";
        }
    }

    if (empty($message)) {
        $sql = "INSERT INTO banners (title, cta_text, cta_link, status, banner_image) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssis", $title, $cta_text, $cta_link, $status, $banner_image);

            if ($stmt->execute()) {
                $message = "Banner added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Add Banner</h2>
    <a href="banners.php" class="text-blue-500 hover:text-blue-700">Back to Banners</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <?php if (!empty($message)): ?>
        <div class="bg-green-200 text-green-800 p-4 mb-4 rounded-lg">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="add_banner.php" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" name="title" id="title" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div class="mb-4">
            <label for="cta_text" class="block text-gray-700 font-bold mb-2">CTA Text</label>
            <input type="text" name="cta_text" id="cta_text" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div class="mb-4">
            <label for="cta_link" class="block text-gray-700 font-bold mb-2">CTA Link</label>
            <input type="text" name="cta_link" id="cta_link" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div class="mb-4">
            <label for="banner_image" class="block text-gray-700 font-bold mb-2">Banner Image</label>
            <input type="file" name="banner_image" id="banner_image" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="status" value="1" class="form-checkbox" checked>
                <span class="ml-2">Active</span>
            </label>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Banner</button>
        </div>
    </form>
</main>
<?php require_once 'includes/footer.php'; ?>

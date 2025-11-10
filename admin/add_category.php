<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = UPLOAD_PATH;
        $image_name = basename($_FILES["image"]["name"]);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        // Create a unique filename
        $image = uniqid() . '.' . $image_ext;
        $target_file = $target_dir . $image;

        // Check if file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // File uploaded successfully
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    }

    if (empty($message)) {
        $sql = "INSERT INTO categories (name, description, image) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $name, $description, $image);

            if ($stmt->execute()) {
                $message = "Category added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    }
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Add Category</h2>
    <a href="list_categories.php" class="text-blue-500 hover:text-blue-700">Back to Categories</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <?php if (!empty($message)): ?>
        <div class="bg-green-200 text-green-800 p-4 mb-4 rounded-lg">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="add_category.php" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Category Name</label>
            <input type="text" name="name" id="name" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" id="description" class="w-full px-3 py-2 border rounded-lg"></textarea>
        </div>
        <div class="mb-4">
            <label for="image" class="block text-gray-700 font-bold mb-2">Image</label>
            <input type="file" name="image" id="image" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Category</button>
        </div>
    </form>
</main>
<?php require_once 'includes/footer.php'; ?>

<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

$message = '';
$category = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM categories WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $category = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image = $_POST['current_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Delete old image
        if(!empty($image) && file_exists(UPLOAD_PATH . $image)) {
            unlink(UPLOAD_PATH . $image);
        }

        $target_dir = UPLOAD_PATH;
        $image_name = basename($_FILES["image"]["name"]);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $image = uniqid() . '.' . $image_ext;
        $target_file = $target_dir . $image;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // New image uploaded
        } else {
            $message = "Error uploading file.";
        }
    }

    if (empty($message)) {
        $sql = "UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssi", $name, $description, $image, $id);
            if ($stmt->execute()) {
                header("Location: list_categories.php");
                exit;
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

if (!$category) {
    header("Location: list_categories.php");
    exit();
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Edit Category</h2>
    <a href="list_categories.php" class="text-blue-500 hover:text-blue-700">Back to Categories</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <form action="edit_category.php?id=<?php echo $category['id']; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
        <input type="hidden" name="current_image" value="<?php echo $category['image']; ?>">
        <!-- Form fields pre-filled with category data -->
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Category Name</label>
            <input type="text" name="name" id="name" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($category['name']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" id="description" class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="image" class="block text-gray-700 font-bold mb-2">Image</label>
            <input type="file" name="image" id="image" class="w-full px-3 py-2 border rounded-lg">
            <?php if(!empty($category['image'])): ?>
                <img src="../uploads/<?php echo htmlspecialchars($category['image']); ?>" class="h-20 mt-2">
            <?php endif; ?>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Category</button>
        </div>
    </form>
</main>
<?php require_once 'includes/footer.php'; ?>

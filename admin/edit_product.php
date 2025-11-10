<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

$message = '';
$product = null;
$product_images = [];

// Fetch categories for the dropdown
$sql_categories = "SELECT id, name FROM categories";
$result_categories = $conn->query($sql_categories);
$categories = [];
if ($result_categories->num_rows > 0) {
    while($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $product = $result->fetch_assoc();
            $sql_images = "SELECT * FROM product_images WHERE product_id = ?";
            $stmt_images = $conn->prepare($sql_images);
            $stmt_images->bind_param("i", $id);
            $stmt_images->execute();
            $result_images = $stmt_images->get_result();
            while($row = $result_images->fetch_assoc()) {
                $product_images[] = $row;
            }
        }
        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $product_name = trim($_POST['product_name']);
    $category_id = trim($_POST['category_id']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $product_type = trim($_POST['product_type']);
    $affiliate_link = trim($_POST['affiliate_link']);

    $conn->begin_transaction();

    try {
        $sql = "UPDATE products SET product_name = ?, category_id = ?, description = ?, price = ?, product_type = ?, affiliate_link = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisdssi", $product_name, $category_id, $description, $price, $product_type, $affiliate_link, $id);
        $stmt->execute();
        $stmt->close();

        if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
            $sql_image = "INSERT INTO product_images (product_id, image) VALUES (?, ?)";
            $stmt_image = $conn->prepare($sql_image);

            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $tmp_name = $_FILES['images']['tmp_name'][$key];
                    $image_name = basename($name);
                    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                    $image_filename = uniqid() . '.' . $image_ext;
                    $target_file = UPLOAD_PATH . $image_filename;

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $stmt_image->bind_param("is", $id, $image_filename);
                        $stmt_image->execute();
                    }
                }
            }
            $stmt_image->close();
        }

        $conn->commit();
        header("Location: list_products.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
}

if (!$product) {
    header("Location: list_products.php");
    exit();
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Edit Product</h2>
    <a href="list_products.php" class="text-blue-500 hover:text-blue-700">Back to Products</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <form action="edit_product.php?id=<?php echo $product['id']; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <div class="mb-4">
            <label for="product_name" class="block text-gray-700 font-bold mb-2">Product Name</label>
            <input type="text" name="product_name" id="product_name" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="category_id" class="block text-gray-700 font-bold mb-2">Category</label>
            <select name="category_id" id="category_id" class="w-full px-3 py-2 border rounded-lg" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $product['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" id="description" class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-gray-700 font-bold mb-2">Price</label>
            <input type="text" name="price" id="price" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="images" class="block text-gray-700 font-bold mb-2">Images</label>
            <input type="file" name="images[]" id="images" class="w-full px-3 py-2 border rounded-lg" multiple>
            <div class="flex flex-wrap mt-4">
                <?php foreach ($product_images as $image): ?>
                    <div class="relative w-32 h-32 m-2">
                        <img src="../uploads/<?php echo htmlspecialchars($image['image']); ?>" class="w-full h-full object-cover rounded">
                        <a href="delete_product_image.php?id=<?php echo $image['id']; ?>&product_id=<?php echo $product['id']; ?>" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1" onclick="return confirm('Are you sure?')">X</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Product Type</label>
            <div class="flex">
                <label class="inline-flex items-center">
                    <input type="radio" name="product_type" value="Own Product" class="form-radio" <?php if ($product['product_type'] == 'Own Product') echo 'checked'; ?>>
                    <span class="ml-2">Own Product</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" name="product_type" value="Affiliate Product" class="form-radio" <?php if ($product['product_type'] == 'Affiliate Product') echo 'checked'; ?>>
                    <span class="ml-2">Affiliate Product</span>
                </label>
            </div>
        </div>
        <div class="mb-4">
            <label for="affiliate_link" class="block text-gray-700 font-bold mb-2">Affiliate Link</label>
            <input type="text" name="affiliate_link" id="affiliate_link" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($product['affiliate_link']); ?>">
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Product</button>
        </div>
    </form>
</main>
<?php require_once 'includes/footer.php'; ?>

<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

$message = '';

// Fetch categories for the dropdown
$sql_categories = "SELECT id, name FROM categories";
$result_categories = $conn->query($sql_categories);
$categories = [];
if ($result_categories->num_rows > 0) {
    while($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name']);
    $category_id = trim($_POST['category_id']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $product_type = trim($_POST['product_type']);
    $affiliate_link = trim($_POST['affiliate_link']);

    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO products (product_name, category_id, description, price, product_type, affiliate_link) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisdss", $product_name, $category_id, $description, $price, $product_type, $affiliate_link);
        $stmt->execute();
        $product_id = $stmt->insert_id;
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
                        $stmt_image->bind_param("is", $product_id, $image_filename);
                        $stmt_image->execute();
                    }
                }
            }
            $stmt_image->close();
        }

        $conn->commit();
        $message = "Product added successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Add Product</h2>
    <a href="list_products.php" class="text-blue-500 hover:text-blue-700">Back to Products</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="product_name" class="block text-gray-700 font-bold mb-2">Product Name</label>
            <input type="text" name="product_name" id="product_name" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="category_id" class="block text-gray-700 font-bold mb-2">Category</label>
            <select name="category_id" id="category_id" class="w-full px-3 py-2 border rounded-lg" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" id="description" class="w-full px-3 py-2 border rounded-lg"></textarea>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-gray-700 font-bold mb-2">Price</label>
            <input type="text" name="price" id="price" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="images" class="block text-gray-700 font-bold mb-2">Images</label>
            <input type="file" name="images[]" id="images" class="w-full px-3 py-2 border rounded-lg" multiple>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Product Type</label>
            <div class="flex">
                <label class="inline-flex items-center">
                    <input type="radio" name="product_type" value="Own Product" class="form-radio" checked>
                    <span class="ml-2">Own Product</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" name="product_type" value="Affiliate Product" class="form-radio">
                    <span class="ml-2">Affiliate Product</span>
                </label>
            </div>
        </div>
        <div class="mb-4">
            <label for="affiliate_link" class="block text-gray-700 font-bold mb-2">Affiliate Link</label>
            <input type="text" name="affiliate_link" id="affiliate_link" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Product</button>
        </div>
    </form>
</main>
<?php require_once 'includes/footer.php'; ?>

<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

// Fetch all products with category names and the first image
$sql = "SELECT p.*, c.name AS category_name, MIN(pi.image) AS image
        FROM products p
        JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_images pi ON p.id = pi.product_id
        GROUP BY p.id
        ORDER BY p.id DESC";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Products</h2>
    <a href="add_product.php" class="text-blue-500 hover:text-blue-700">Add New Product</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <table class="w-full whitespace-no-wrap">
        <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                <th class="px-4 py-3">Image</th>
                <th class="px-4 py-3">Product Name</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">
                            <?php if (!empty($product['image'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="h-12 w-12 object-cover rounded">
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td class="px-4 py-3">$<?php echo htmlspecialchars($product['price']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($product['product_type']); ?></td>
                        <td class="px-4 py-3">
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="ml-4 text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="text-gray-700">
                    <td colspan="6" class="px-4 py-3 text-center">No products found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php require_once 'includes/footer.php'; ?>

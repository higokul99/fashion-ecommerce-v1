<?php
require_once 'config.php';

// Fetch all categories for the filter dropdown
$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);
$categories = [];
if ($result_categories->num_rows > 0) {
    while($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Base SQL query for products
$sql_products = "SELECT p.*, MIN(pi.image) AS image
                 FROM products p
                 LEFT JOIN product_images pi ON p.id = pi.product_id";
$params = [];
$types = '';

// Filter by category
if (!empty($_GET['category_id'])) {
    $sql_products .= " WHERE p.category_id = ?";
    $params[] = $_GET['category_id'];
    $types .= 'i';
}

$sql_products .= " GROUP BY p.id ORDER BY p.created_at DESC";

// Fetch products
$stmt = $conn->prepare($sql_products);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result_products = $stmt->get_result();
$products = [];
if ($result_products->num_rows > 0) {
    while($row = $result_products->fetch_assoc()) {
        $products[] = $row;
    }
}

include 'includes/header.php';
?>

<div class="container mx-auto px-6">
    <h1 class="text-3xl font-bold text-gray-800 my-6">All Products</h1>

    <!-- Filtering -->
    <div class="flex justify-between items-center mb-6">
        <form action="products.php" method="get" class="flex items-center">
            <label for="category" class="mr-2">Category:</label>
            <select name="category_id" id="category" class="border border-gray-300 rounded-md py-2 px-4" onchange="this.form.submit()">
                <option value="">All</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php if(isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($products as $product): ?>
        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all">
            <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="rounded-t-xl h-64 w-full object-cover">
            </a>
            <div class="p-4">
                <h3 class="font-semibold"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                <p class="text-gray-600 mt-1">$<?php echo htmlspecialchars($product['price']); ?></p>
                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="mt-4 block text-center bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition-all">Buy Now</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

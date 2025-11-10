<?php
require_once 'config.php';

$product = null;
$product_images = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?";
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

if (!$product) {
    // Redirect or show a 404 not found error
    header("Location: products.php");
    exit;
}

include 'includes/header.php';
?>

<div class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Image Gallery -->
        <div>
            <?php if (!empty($product_images)): ?>
                <img src="uploads/<?php echo htmlspecialchars($product_images[0]['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="rounded-lg shadow-lg w-full">
                <div class="grid grid-cols-4 gap-4 mt-4">
                    <?php foreach($product_images as $image): ?>
                        <img src="uploads/<?php echo htmlspecialchars($image['image']); ?>" alt="Thumbnail" class="rounded-lg shadow-md cursor-pointer">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <p class="text-gray-600 mt-2">Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
            <p class="text-2xl text-gray-800 font-semibold mt-4">$<?php echo htmlspecialchars($product['price']); ?></p>
            <p class="text-gray-600 mt-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <!-- Buy Now Button -->
            <div class="mt-6">
                <?php if ($product['product_type'] == 'Affiliate Product' && !empty($product['affiliate_link'])): ?>
                    <a href="<?php echo htmlspecialchars($product['affiliate_link']); ?>" target="_blank" class="block text-center bg-blue-500 text-white py-3 rounded-md hover:bg-blue-600 transition-all text-lg font-semibold">Buy Now</a>
                <?php else: ?>
                    <a href="#" class="block text-center bg-blue-500 text-white py-3 rounded-md hover:bg-blue-600 transition-all text-lg font-semibold">Buy Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

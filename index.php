<?php
require_once 'config.php';

// Fetch active banners
$sql_banners = "SELECT * FROM banners WHERE status = 1 ORDER BY id DESC";
$result_banners = $conn->query($sql_banners);
$banners = [];
if ($result_banners->num_rows > 0) {
    while($row = $result_banners->fetch_assoc()) {
        $banners[] = $row;
    }
}

// Fetch top categories
$sql_categories = "SELECT * FROM categories LIMIT 6";
$result_categories = $conn->query($sql_categories);
$categories = [];
if ($result_categories->num_rows > 0) {
    while($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch trending products
$sql_products = "SELECT p.*, MIN(pi.image) AS image
                 FROM products p
                 LEFT JOIN product_images pi ON p.id = pi.product_id
                 GROUP BY p.id
                 ORDER BY p.created_at DESC
                 LIMIT 8";
$result_products = $conn->query($sql_products);
$products = [];
if ($result_products->num_rows > 0) {
    while($row = $result_products->fetch_assoc()) {
        $products[] = $row;
    }
}

include 'includes/header.php';
?>
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<div class="container mx-auto px-6">
    <!-- Hero Banner -->
    <div class="swiper-container h-96 rounded-md shadow-lg">
        <div class="swiper-wrapper">
            <?php foreach ($banners as $banner): ?>
                <div class="swiper-slide bg-cover bg-center" style="background-image: url('uploads/<?php echo htmlspecialchars($banner['banner_image']); ?>')">
                    <div class="h-full flex items-center justify-center">
                        <div class="text-center">
                            <h1 class="text-white text-4xl font-bold"><?php echo htmlspecialchars($banner['title']); ?></h1>
                            <a href="<?php echo htmlspecialchars($banner['cta_link']); ?>" class="mt-4 inline-block bg-white text-gray-800 font-semibold py-2 px-4 rounded-md hover:bg-gray-200 transition-all"><?php echo htmlspecialchars($banner['cta_text']); ?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>


    <!-- Feature Badges -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mt-12">
        <div class="flex items-center bg-white p-4 rounded-lg shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div class="ml-4">
                <h3 class="font-semibold">7 Days Easy Return</h3>
                <p class="text-gray-600 text-sm">On all products</p>
            </div>
        </div>
        <div class="flex items-center bg-white p-4 rounded-lg shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01" /></svg>
            <div class="ml-4">
                <h3 class="font-semibold">Cash on Delivery</h3>
                <p class="text-gray-600 text-sm">Available</p>
            </div>
        </div>
        <div class="flex items-center bg-white p-4 rounded-lg shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <div class="ml-4">
                <h3 class="font-semibold">Lowest Prices</h3>
                <p class="text-gray-600 text-sm">Guaranteed</p>
            </div>
        </div>
        <div class="flex items-center bg-white p-4 rounded-lg shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div class="ml-4">
                <h3 class="font-semibold">Authentic Products</h3>
                <p class="text-gray-600 text-sm">Direct from brands</p>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Top Categories</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php foreach ($categories as $category): ?>
            <div class="text-center">
                <a href="products.php?category_id=<?php echo $category['id']; ?>" class="block bg-white p-4 rounded-full shadow-md hover:shadow-xl transition-all">
                    <img src="uploads/<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="w-24 h-24 rounded-full mx-auto object-cover">
                </a>
                <p class="mt-2 font-semibold"><?php echo htmlspecialchars($category['name']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Trending Products -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Trending Products</h2>
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
</div>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper('.swiper-container', {
        pagination: {
            el: '.swiper-pagination',
        },
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
    });
</script>

<?php include 'includes/footer.php'; ?>

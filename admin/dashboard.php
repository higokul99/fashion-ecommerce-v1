<?php
require_once 'auth.php';
require_once 'includes/header.php';
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
    <a href="logout.php" class="text-blue-500 hover:text-blue-700">Logout</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <h3 class="text-gray-700 text-3xl font-medium">Welcome to the Admin Dashboard</h3>
    <p class="mt-2 text-gray-600">Here you can manage your categories, products, and banners.</p>
</main>
<?php require_once 'includes/footer.php'; ?>

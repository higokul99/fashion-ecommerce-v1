<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Admin Panel</h1>
            </div>
            <nav class="mt-6">
                <a href="dashboard.php" class="block py-2 px-4 text-sm text-gray-300 hover:bg-gray-700">Dashboard</a>
                <a href="list_categories.php" class="block py-2 px-4 text-sm text-gray-300 hover:bg-gray-700">Categories</a>
                <a href="list_products.php" class="block py-2 px-4 text-sm text-gray-300 hover:bg-gray-700">Products</a>
                <a href="banners.php" class="block py-2 px-4 text-sm text-gray-300 hover:bg-gray-700">Banners</a>
                <a href="logout.php" class="block py-2 px-4 text-sm text-gray-300 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

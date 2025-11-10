<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

// Fetch all categories from the database
$sql = "SELECT * FROM categories ORDER BY id DESC";
$result = $conn->query($sql);
$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Categories</h2>
    <a href="add_category.php" class="text-blue-500 hover:text-blue-700">Add New Category</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <table class="w-full whitespace-no-wrap">
        <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                <th class="px-4 py-3">Image</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Description</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">
                            <?php if (!empty($category['image'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="h-12 w-12 object-cover rounded">
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($category['name']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($category['description']); ?></td>
                        <td class="px-4 py-3">
                            <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="ml-4 text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="text-gray-700">
                    <td colspan="4" class="px-4 py-3 text-center">No categories found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php require_once 'includes/footer.php'; ?>

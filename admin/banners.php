<?php
require_once 'auth.php';
require_once '../config.php';
require_once 'includes/header.php';

// Fetch all banners from the database
$sql = "SELECT * FROM banners ORDER BY id DESC";
$result = $conn->query($sql);
$banners = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $banners[] = $row;
    }
}
?>
<header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
    <h2 class="text-2xl font-semibold text-gray-800">Banners</h2>
    <a href="add_banner.php" class="text-blue-500 hover:text-blue-700">Add New Banner</a>
</header>
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
    <table class="w-full whitespace-no-wrap">
        <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                <th class="px-4 py-3">Image</th>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y">
            <?php if (!empty($banners)): ?>
                <?php foreach ($banners as $banner): ?>
                    <tr class="text-gray-700">
                        <td class="px-4 py-3">
                            <img src="../uploads/<?php echo htmlspecialchars($banner['banner_image']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" class="h-12">
                        </td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($banner['title']); ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 font-semibold leading-tight <?php echo $banner['status'] ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'; ?> rounded-full">
                                <?php echo $banner['status'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="edit_banner.php?id=<?php echo $banner['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <a href="delete_banner.php?id=<?php echo $banner['id']; ?>" class="ml-4 text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this banner?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="text-gray-700">
                    <td colspan="4" class="px-4 py-3 text-center">No banners found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<?php require_once 'includes/footer.php'; ?>

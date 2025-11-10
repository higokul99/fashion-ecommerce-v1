<?php
session_start();
require_once '../config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT id, password FROM admins WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                header("Location: dashboard.php");
                exit;
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No account found with that username.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-xs">
        <form action="index.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold mb-6 text-center">Admin Login</h1>
            <?php if (!empty($message)): ?>
                <p class="text-red-500 text-xs italic mb-4"><?php echo $message; ?></p>
            <?php endif; ?>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" name="username" type="text" placeholder="Username">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="******************">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Sign In
                </button>
            </div>
        </form>
    </div>
</body>
</html>

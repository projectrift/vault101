<?php
session_start();

// Check if the user is logged in and is an admin
$isAdmin = isset($_SESSION['username']) && $_SESSION['is_admin'];

// Function to retrieve posts from the database
function getPostsFromDatabase()
{
    // Replace with your database connection details
    $conn = mysqli_connect('localhost', 'username', 'password', 'database_name');

    // Retrieve posts from the database
    $query = "SELECT * FROM posts ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);

    return $posts;
}

// Function to save a new post to the database
function savePostToDatabase($title, $content, $image)
{
    // Replace with your database connection details
    $conn = mysqli_connect('localhost', 'username', 'password', 'database_name');

    // Prepare and execute the insert query
    $stmt = mysqli_prepare($conn, "INSERT INTO posts (title, content, image, created_at) VALUES (?, ?, ?, NOW())");
    mysqli_stmt_bind_param($stmt, "sss", $title, $content, $image);
    mysqli_stmt_execute($stmt);

    mysqli_close($conn);
}

// Check if a new post is being submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin && isset($_POST['title']) && isset($_POST['content'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = $_FILES['image']['name'];
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageUploadPath = 'uploads/' . $imageName;
        
        // Move uploaded image to the desired location
        move_uploaded_file($imageTmpPath, $imageUploadPath);
        
        $image = $imageUploadPath;
    }

    // Save the new post to the database
    savePostToDatabase($title, $content, $image);
}

// Retrieve all posts from the database
$posts = getPostsFromDatabase();
?>

<!DOCTYPE html>
<html>
<head>
    <title>SA-MP Website</title>
    <style>
        /* Add your CSS styling here */
    </style>
</head>
<body>
    <header>
        <img src="banner-image.jpg" alt="SA-MP Banner">
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Downloads</a></li>
                <li><a href="#">Forums</a></li>
                <li><a href="#">Servers</a></li>
                <li><a href="#">Shop</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <?php if (isset($_SESSION['username'])): ?>
            <?php if ($isAdmin): ?>
                                <h1>Welcome, <?php echo $_SESSION['username']; ?> (Admin)</h1>
                <h2>Create a New Post</h2>
                <form action="index.php" method="POST" enctype="multipart/form-data">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                    <br>
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" required></textarea>
                    <br>
                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image">
                    <br>
                    <input type="submit" value="Create Post">
                </form>
            <?php else: ?>
                <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
                <h2>Regular User</h2>
                <!-- Add regular user content here -->
            <?php endif; ?>
        <?php else: ?>
            <h1>Please login or register</h1>
            <!-- Add login and registration forms here -->
        <?php endif; ?>

        <h2>Latest Posts</h2>
        <?php foreach ($posts as $post): ?>
            <h3><?php echo $post['title']; ?></h3>
            <p><?php echo $post['content']; ?></p>
            <?php if ($post['image']): ?>
                <img src="<?php echo $post['image']; ?>" alt="Post Image">
            <?php endif; ?>
            <hr>
        <?php endforeach; ?>
    </main>
    <footer>
        <!-- Add your footer content here -->
    </footer>
</body>
</html>



<?php
include('includes/db.php');
include('includes/header.php');

// Обработка загрузки изображения с текстом
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && !isset($_POST['edit'])) {
    $imagePath = 'uploads/' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

    $imageText = $_POST['image_text'];

    $stmt = $conn->prepare("INSERT INTO images (image_path, image_text) VALUES (?, ?)");
    $stmt->bind_param("ss", $imagePath, $imageText);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Обработка удаления изображения
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Обработка редактирования изображения и текста
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $imageText = $_POST['image_text'];
    
    // Если загружено новое изображение, обновим путь к изображению
    if (isset($_FILES['new_image']) && $_FILES['new_image']['size'] > 0) {
        $newImagePath = 'uploads/' . basename($_FILES['new_image']['name']);
        move_uploaded_file($_FILES['new_image']['tmp_name'], $newImagePath);
        
        $stmt = $conn->prepare("UPDATE images SET image_path = ?, image_text = ? WHERE id = ?");
        $stmt->bind_param("ssi", $newImagePath, $imageText, $id);
    } else {
        $stmt = $conn->prepare("UPDATE images SET image_text = ? WHERE id = ?");
        $stmt->bind_param("si", $imageText, $id);
    }
    
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$images = $conn->query("SELECT * FROM images");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Images</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        form {
            margin-bottom: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form input[type="file"],
        form textarea,
        form input[type="text"],
        form button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background: #fff;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        li img {
            display: block;
            margin-bottom: 10px;
        }
        li form {
            display: flex;
            flex-direction: column;
        }
        li form textarea,
        li form input[type="file"],
        li form button {
            margin-bottom: 10px;
        }
        a {
            color: #d9534f;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .ul_nav {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: center;
            border-bottom: 1px solid black;
        }
        .li_nav {
            background: #fff;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
            margin-right: 20px;
        }
        .a_nav {
            color: #d9534f;
            text-decoration: none;
            font-size: 20px;
        }

        .images_container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .image-redact {
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
            margin-left: 30px;
        }
        .image-redact-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: column;
            align-items: center;
            width: 300px;
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="images_container">
    <h1>Manage Images</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="image" required>
        <textarea name="image_text" placeholder="Enter image text" required></textarea>
        <button type="submit">Upload Image</button>
    </form>
    </div>
    <h2>Uploaded Images</h2>
    <ul class="image-redact">
        <?php while ($row = $images->fetch_assoc()): ?>
            <li class="image-redact-container">
                <img src="<?php echo $row['image_path']; ?>" alt="Image" width="100">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <textarea name="image_text" placeholder="Enter image text" required><?php echo $row['image_text']; ?></textarea>
                    <input type="file" name="new_image">
                    <button type="submit" name="edit">Save</button>
                </form>
                <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
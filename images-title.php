<?php
include('includes/db.php');
include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $imagePath = 'uploads/' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    $text = $_POST['text'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO images_title (image_path, text, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $imagePath, $text, $price);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM images_title WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $imagePath = !empty($_FILES['image']['name']) ? 'uploads/' . basename($_FILES['image']['name']) : $_POST['existing_image_path'];
    if (!empty($_FILES['image']['name'])) {
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }
    $text = $_POST['text'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE images_title SET image_path = ?, text = ?, price = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $imagePath, $text, $price, $id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$images_title = $conn->query("SELECT * FROM images_title");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Images & Titles</title>
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
        form input[type="text"],
        form input[type="number"],
        form button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
        }
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            resize: vertical;
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
        li form input[type="file"],
        li form input[type="text"],
        li form input[type="number"],
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
    <h1>Manage Images & Titles</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="image" required>
        <input type="text" name="text" placeholder="Text" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <button type="submit" name="submit">Upload</button>
    </form>
    </div>
    <h2>Uploaded Images with Titles</h2>
    <ul class="image-redact">
        <?php while ($row = $images_title->fetch_assoc()): ?>
            <li class="image-redact-container">
                <img src="<?php echo $row['image_path']; ?>" alt="Image" width="100">
                <p><?php echo $row['text']; ?> - $<?php echo $row['price']; ?></p>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="existing_image_path" value="<?php echo $row['image_path']; ?>">
                    <input type="file" name="image">
                    <input type="text" name="text" value="<?php echo $row['text']; ?>" required>
                    <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" required>
                    <button type="submit" name="update">Update</button>
                </form>
                <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
<?php
include('includes/db.php');

// Проверяем, была ли отправлена форма для регистрации
$registrationCompleted = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
  $login = $_POST['login'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  $stmt = $conn->prepare("INSERT INTO users (login, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $login, $email, $password);
  $stmt->execute();

  // Устанавливаем флаг, чтобы при обновлении страницы форма не отправлялась повторно
  $registrationCompleted = true;

  // Перенаправляем пользователя на эту же страницу методом GET
  header("Location: {$_SERVER['PHP_SELF']}");
  exit();
}

$images = $conn->query("SELECT * FROM images");
$images_title = $conn->query("SELECT * FROM images_title");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Site</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    ul {
    list-style-type: none;
}
  </style>
</head>
<body>
  <h1>Register</h1>
  <?php if (!$registrationCompleted): ?>
    <form method="POST">
      <input type="text" name="login" placeholder="Login" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="register">Register</button>
    </form>
  <?php else: ?>
    <p>Registration successful!</p>
  <?php endif; ?>
  <h2>Images</h2>
  <ul>
    <?php while ($row = $images->fetch_assoc()): ?>
      <li><img src="<?php echo $row['image_path']; ?>" alt="Image" width="100"></li>
      <p><?php echo $row['image_text']; ?></p>
    <?php endwhile; ?>
  </ul>
  <h2>Images with Titles and Prices</h2>
  <ul>
    <?php while ($row = $images_title->fetch_assoc()): ?>
      <li>
        <img src="<?php echo $row['image_path']; ?>" alt="Image" width="100">
        <p><?php echo $row['text']; ?> - $<?php echo $row['price']; ?></p>
      </li>
    <?php endwhile; ?>
  </ul>
</body>
</html>
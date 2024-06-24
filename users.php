<?php
include('includes/db.php');
include('includes/header.php');

if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: users.php");
  exit();
}

if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $result = $conn->query("SELECT * FROM users WHERE id = $id");
  $user = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
  $id = $_POST['id'];
  $login = $_POST['login'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  $stmt = $conn->prepare("UPDATE users SET login = ?, email = ?, password = ? WHERE id = ?");
  $stmt->bind_param("sssi", $login, $email, $password, $id);
  $stmt->execute();
  header("Location: users.php");
  exit();
}

$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f4f4f4;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    form {
      max-width: 500px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    form input, form button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    form button {
      background-color: #28a745;
      color: #fff;
      border: none;
      cursor: pointer;
    }
    form button:hover {
      background-color: #218838;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 12px;
      text-align: left;
    }
    th {
      background-color: #f4f4f4;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .actions a {
      margin: 0 5px;
      text-decoration: none;
      color: #007bff;
    }
    .actions a:hover {
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
  </style>
</head>
<body>
  <h1>Manage Users</h1>
  <?php if (isset($user)): ?>
    <h2>Edit User</h2>
    <form method="POST">
      <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
      <input type="text" name="login" value="<?php echo $user['login']; ?>" required>
      <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
      <input type="password" name="password" placeholder="New Password" required>
      <button type="submit" name="update">Update User</button>
    </form>
  <?php endif; ?>
  <h2>Users List</h2>
  <table>
    <tr>
      <th>Login</th>
      <th>Email</th>
      <th>Password</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = $users->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['login']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['password']; ?></td>
        <td class="actions">
          <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
          <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
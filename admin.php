<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .admin_nav {
            display: flex;
            align-items: center;
            justify-content: center;
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
            width: 200px;
            text-align: center;
        }
        a {
            color: #d9534f;
            text-decoration: none;
            font-size: 24px;
        }
        a:hover {
            text-decoration: underline;
        }

        h1 {
            font-size: 46px;
            margin-top: 250px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to the Admin Panel</h1>
    </header>

    <main>
        <div class="admin_nav">
            <?php
            include('includes/db.php');
            include('includes/header.php');
            ?>
        </div>
    </main>
</body>
</html>
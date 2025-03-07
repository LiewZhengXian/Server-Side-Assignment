<?php include('db.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Create New Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h2>Create New Post</h2>
        <form action="add_post.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="title" placeholder="Post Title" required>
            </div>
            <div class="form-group">
                <textarea class="form-control" name="content" placeholder="Post Content" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Post</button>
        </form>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $user_id = 1;  // Assuming user 1 is logged in for now

        $sql = "INSERT INTO posts (user_id, title, content) VALUES ('$user_id', '$title', '$content')";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    ?>
</body>
</html>

<?php include('db.php');
$post_id = $_POST['post_id'];
$content = $_POST['content'];
$user_id = 1;  // Assuming user 1 is logged in

$sql = "INSERT INTO comments (post_id, user_id, content) VALUES ('$post_id', '$user_id', '$content')";
$conn->query($sql);
header("Location: post.php?id=$post_id");
?>

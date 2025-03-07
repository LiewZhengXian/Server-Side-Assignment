<?php 
require "../user_module/database.php";
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    

    // Query to fetch comments for the specific recipe
    $recipe_id = $row["recipe_id"];
    $sql2 = "SELECT c.content, u.username, c.creation_datetime 
             FROM Comment c, post p, user u
             WHERE c.comment_id = p.comment_id AND c.comment_id = u.comment_id AND p.post_id = ?
             ORDER BY c.creation_datetime DESC";

    // Prepare and bind statement for security (to prevent SQL injection)
    $stmt = $con->prepare($sql2);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $comments = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="comment-box">';
            echo '<div class="d-flex justify-content-between">';
            echo '<div class="d-flex align-items-center mb-1">';
            echo '<img src="/api/placeholder/32/32" class="rounded-circle me-2" alt="User avatar">';
            echo '<div>';
            echo '<h6 class="mb-0">' . htmlspecialchars($row["username"]) . '</h6>';
            echo '<small class="text-muted">' . htmlspecialchars($row["creation_datetime"]) . '</small>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<p class="small mb-1">' . htmlspecialchars($row["content"]) . '.</p>';
            echo '</div>';
        }
    } else {
        echo '<p>No comments yet. Be the first to comment!</p>';
    }

    $con->close();
}
?>
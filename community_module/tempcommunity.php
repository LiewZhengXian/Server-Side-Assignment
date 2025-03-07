<!-- Recipe Grid -->
<div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
    <?php
    // Check if there are results
    if ($result->num_rows > 0) {
        // Loop through each recipe
        while($row = $result->fetch_assoc()) {
            // Format the rating to 1 decimal place
            $rating = number_format($row["avg_rating"], 1);
            
            // Determine rating color based on value
            $ratingClass = "bg-danger";
            if ($rating >= 4.0) {
                $ratingClass = "bg-success";
            } else if ($rating >= 3.0) {
                $ratingClass = "bg-warning";
            }
            
            // Generate the recipe card
            ?>
            <div class="col">
                <div class="card h-100 recipe-card">
                    <?php if (!empty($row["image_url"])) { ?>
                        <img src="<?php echo htmlspecialchars($row["image_url"]); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row["title"]); ?>">
                    <?php } else { ?>
                        <img src="/api/placeholder/400/200" class="card-img-top" alt="Recipe Image">
                    <?php } ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title"><?php echo htmlspecialchars($row["title"]); ?></h5>
                            <div class="recipe-rating">
                                <span class="badge <?php echo $ratingClass; ?>"><?php echo $rating; ?> ★</span>
                            </div>
                        </div>
                        <p class="card-text small"><?php echo htmlspecialchars($row["description"]); ?></p>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="rating-stars">
                                <?php
                                // Generate stars based on rating
                                $fullStars = floor($rating);
                                $halfStar = $rating - $fullStars >= 0.5;
                                
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $fullStars) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else if ($i == $fullStars + 1 && $halfStar) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#commentModal<?php echo $row["id"]; ?>">
                                <i class="fas fa-comment"></i> Comments (<?php echo $row["comment_count"]; ?>)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        // Display a message if no recipes are found
        ?>
        <div class="col-12">
            <div class="alert alert-info">
                No recipes found. Be the first to add a recipe!
            </div>
        </div>
        <?php
    }
    $con->close();
    ?>
</div>
    <!-- Comment Modal for Recipe 1 -->
    <div class="modal fade" id="commentModal1" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Pasta Carbonara - Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Rating Section -->
                    <div class="mb-4">
                        <h6>Rate this recipe:</h6>
                        <div class="rating-stars fs-4">
                            <i class="far fa-star" data-rating="1"></i>
                            <i class="far fa-star" data-rating="2"></i>
                            <i class="far fa-star" data-rating="3"></i>
                            <i class="far fa-star" data-rating="4"></i>
                            <i class="far fa-star" data-rating="5"></i>
                        </div>
                    </div>
                    
                    <!-- Comment Form -->
                    <form class="mb-4">
                        <div class="mb-3">
                            <label for="commentText" class="form-label">Add your comment or tip:</label>
                            <textarea class="form-control" id="commentText" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                    
                    <!-- Comment List -->
                    <h6>Comments (12)</h6>
                    <div class="comment-list">
                        <!-- Comment 1 -->
                        <div class="comment-box">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center mb-1">
                                    <img src="/api/placeholder/32/32" class="rounded-circle me-2" alt="User avatar">
                                    <div>
                                        <h6 class="mb-0">Julia K.</h6>
                                        <small class="text-muted">2 days ago</small>
                                    </div>
                                </div>
                                <div class="rating-stars small">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <p class="small mb-1">I added extra pepper and it was amazing! Everyone loved it.</p>
                            <div class="d-flex mb-2">
                                <button class="btn btn-sm btn-outline-secondary me-2">
                                    <i class="fas fa-thumbs-up"></i> Helpful (8)
                                </button>
                                <button class="btn btn-sm btn-outline-secondary reply-toggle" data-target="reply1">
                                    Reply
                                </button>
                            </div>
                            <div id="reply1" class="reply-form mb-2" style="display: none;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" placeholder="Write a reply...">
                                    <button class="btn btn-sm btn-primary">Send</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Comment 2 -->
                        <div class="comment-box">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center mb-1">
                                    <img src="/api/placeholder/32/32" class="rounded-circle me-2" alt="User avatar">
                                    <div>
                                        <h6 class="mb-0">Marco T.</h6>
                                        <small class="text-muted">1 week ago</small>
                                    </div>
                                </div>
                                <div class="rating-stars small">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                            <p class="small mb-1">The recipe was good but too rich for my taste. I'd use less cheese next time.</p>
                            <div class="d-flex mb-2">
                                <button class="btn btn-sm btn-outline-secondary me-2">
                                    <i class="fas fa-thumbs-up"></i> Helpful (3)
                                </button>
                                <button class="btn btn-sm btn-outline-secondary reply-toggle" data-target="reply2">
                                    Reply
                                </button>
                            </div>
                            <div id="reply2" class="reply-form mb-2" style="display: none;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" placeholder="Write a reply...">
                                    <button class="btn btn-sm btn-primary">Send</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <button class="btn btn-sm btn-outline-primary">Load More Comments</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Comment Modals for other recipes would be similar but with different content -->
    <div class="modal fade" id="commentModal2" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chocolate Brownies - Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Similar structure as the first modal -->
                    <p class="text-center text-muted">Comment content would go here</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="commentModal3" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Greek Salad - Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Similar structure as the first modal -->
                    <p class="text-center text-muted">Comment content would go here</p>
                </div>
            </div>
        </div>
    </div>
<!-- Comment Modals - You would need to generate these dynamically too -->
<?php
// Reopen connection
$con = new mysqli($servername, $username, $password, $dbname);

if ($con->conect_error) {
    die("Connection failed: " . $con->connect_error);
}

$sql = "SELECT id, title FROM Recipe ORDER BY creation_date DESC";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $recipeId = $row["id"];
        $recipeTitle = $row["title"];
        ?>
        <!-- Comment Modal for <?php echo htmlspecialchars($recipeTitle); ?> -->
        <div class="modal fade" id="commentModal<?php echo $recipeId; ?>" tabindex="-1" aria-labelledby="commentModalLabel<?php echo $recipeId; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel<?php echo $recipeId; ?>">Comments for <?php echo htmlspecialchars($recipeTitle); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php
                        // Fetch comments for this recipe
                        $commentSql = "SELECT c.content, c.timestamp, u.username 
                                      FROM Comment c 
                                      JOIN User u ON c.user_id = u.user_id 
                                      WHERE c.recipe_id = $recipeId 
                                      ORDER BY c.timestamp DESC";
                        $commentResult = $con->query($commentSql);
                        
                        if ($commentResult->num_rows > 0) {
                            while($commentRow = $commentResult->fetch_assoc()) {
                                ?>
                                <div class="comment mb-3">
                                    <div class="d-flex justify-content-between">
                                        <h6><?php echo htmlspecialchars($commentRow["username"]); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($commentRow["timestamp"]); ?></small>
                                    </div>
                                    <p><?php echo htmlspecialchars($commentRow["content"]); ?></p>
                                    <hr>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p class="text-center">No comments yet. Be the first to comment!</p>';
                        }
                        ?>
                        
                        <!-- Comment Form -->
                        <form id="commentForm<?php echo $recipeId; ?>" class="mt-3">
                            <input type="hidden" name="recipe_id" value="<?php echo $recipeId; ?>">
                            <div class="mb-3">
                                <label for="commentContent<?php echo $recipeId; ?>" class="form-label">Add a comment:</label>
                                <textarea class="form-control" id="commentContent<?php echo $recipeId; ?>" name="content" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
$con->close();
?>

<!-- JavaScript for handling comment submission -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all comment forms
    const commentForms = document.querySelectorAll('[id^="commentForm"]');
    
    // Add event listener to each form
    commentForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Submit comment via AJAX
            fetch('submit_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form
                    this.reset();
                    
                    // Refresh comments (you could implement a more elegant solution)
                    location.reload();
                } else {
                    alert('Error submitting comment: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting your comment.');
            });
        });
    });
});
</script>
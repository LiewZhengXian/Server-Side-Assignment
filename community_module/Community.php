<?php
session_start();
include '../user_module/database.php';


$sql = "SELECT p.post_id, p.title, p.content, 
       r.title AS recipe_title, r.description AS recipe_desc, 
       r.image_url AS recipe_image, u.username, r.recipe_id,
       (SELECT AVG(rating_value) FROM Rating rt WHERE rt.post_id = p.post_id) AS avg_rating 
        FROM Post p 
        LEFT JOIN Recipe r ON p.recipe_id = r.recipe_id 
        JOIN User u ON p.user_id = u.user_id 
        ORDER BY p.creation_datetime DESC;";
$result = $con->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Community</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .rating-stars {
            color: #ffc107;
            cursor: pointer;
        }

        .post-rating-stars {
            color: #ffc107;
            cursor: pointer;
        }

        .comment-box {
            border-left: 3px solid #6c757d;
            padding-left: 15px;
            margin-bottom: 15px;
        }

        .recipe-card {
            transition: transform 0.3s;
        }

        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .add-post-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .hover-form {
            display: none;
            position: absolute;
            right: 0;
            bottom: 60px;
            width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            padding: 15px;
        }

        .add-post-container:hover .hover-form {
            display: block;
        }
    </style>
</head>


<!-- Simple Navbar -->
<?php include "../navbar.php"?>

<div class="container my-4">
    <h2 class="mb-4">Community Recipes</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <?php
        // Check if there are results
        if ($result->num_rows > 0) {
            // Loop through each recipe
            while ($row = $result->fetch_assoc()) {
                // Format the rating to 1 decimal place
                $rating = number_format($row["avg_rating"], 1);

                $ratingClass = "bg-danger";
                if ($rating >= 4.0) {
                    $ratingClass = "bg-success";
                } else if ($rating >= 3.0) {
                    $ratingClass = "bg-warning";
                } else if ($rating == 0) {
                    $rating = "N/A";
                    $ratingClass = "bg-secondary";
                }
                $post_id = $row["post_id"];
                $user_id = $_SESSION["user_id"];

                $sql2 = "SELECT c.comment_id, c.content, c.creation_datetime, u.username 
                            FROM Comment c
                            JOIN User u ON c.user_id = u.user_id
                            WHERE c.post_id = ?
                            ORDER BY c.creation_datetime DESC";

                $stmt = $con->prepare($sql2);
                $stmt->bind_param('i', $post_id);
                $stmt->execute();
                $comments = $stmt->get_result();
                // Generate the recipe card
                // Fetch the user's existing rating for this post
                $rating_user_id = $_SESSION["user_id"];
                $rating_post_id = $row["post_id"];
                $rating_query = "SELECT rating_value FROM Rating WHERE user_id = ? AND post_id = ?";
                $stmt2 = $con->prepare($rating_query);
                $stmt2->bind_param("ii", $rating_user_id, $rating_post_id);
                $stmt2->execute();
                $rating_result = $stmt2->get_result();
                $user_rating = $rating_result->fetch_assoc();
                $existing_rating = $user_rating['rating_value'] ?? 0; // Default to 0 if no rating exists
                ?>
                <!-- View Recipe Modal -->
                <div class="modal fade" id="viewModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">Recipe Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" id="recipeDetails">
                                <!-- Recipe details will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 recipe-card">

                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title"><?php echo htmlspecialchars($row["title"]); ?></h5> <br>


                                <div class="recipe-rating">
                                    <span class="badge <?php echo $ratingClass; ?>"><?php echo $rating; ?> ★</span>
                                </div>
                            </div>
                            <?php if (!empty($row["recipe_image"])) { ?>
                                <div style="width: 200px; height: 200px; overflow: hidden; display: block;">
                                    <img src="<?php echo htmlspecialchars($row["recipe_image"]); ?>" width="200" height="200"
                                        class="card-img-top" alt="<?php echo htmlspecialchars($row["title"]); ?>">
                                </div>

                            <?php } else { ?>

                            <?php } ?>

                            <p class="card-text small"><?php echo htmlspecialchars($row["content"]); ?></p>

                            <?php if (!empty($row["recipe_title"])) { ?>
                                <button class="btn btn-primary btn-sm mt-2" type="button"
                                    onclick="loadRecipeDetails(<?php echo $row['recipe_id']; ?>)" data-bs-toggle="modal"
                                    data-bs-target="#viewModal">
                                    Show Recipe
                                </button>

                            <?php } ?>


                            <!-- Collapsible content -->
                            <div class="collapse mt-2" id="collapse<?php echo $row['post_id']; ?>">

                                <p class="card-text"><?php echo htmlspecialchars($row["recipe_title"]); ?></p>
                                <p class="card-text small"><?php echo nl2br($row["recipe_desc"]); ?></p>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="post-rating-stars">
                                    <?php
                                    // Generate stars based on rating
                                    if (is_numeric($rating)) {
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
                                    } else {
                                        echo "No ratings yet";
                                    }
                                    ?>
                                    <h6 class="card-text text-muted fst-italic">
                                        posted by: <?php echo htmlspecialchars($row["username"]); ?></h6>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#commentModal<?php echo $row["post_id"]; ?>">
                                    <i class="fas fa-comment"></i> Comments (<?php echo $comments->num_rows; ?>)
                                </button>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="commentModal<?php echo $row["post_id"]; ?>" tabindex="-1"
                    aria-labelledby="commentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="commentModalLabel">
                                    <?php echo htmlspecialchars($row["title"]); ?> -
                                    Comments
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Rating Section -->
                                <div class="mb-4">
                                    <h6>Rate this recipe:</h6>
                                    <form id="ratingForm" action="Add_rating.php" method="POST">
                                        <input type="hidden" name="post_id" value="<?= $post_id ?>">
                                        <input type="hidden" name="user_id" value="<?= $user_id ?>">
                                        <input type="hidden" name="rating" id="selectedRating" value="<?= $existing_rating ?>">

                                        <div class="rating-stars fs-4" data-existing-rating="<?= $existing_rating ?>">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa-star <?= $i <= $existing_rating ? 'fas' : 'far' ?>"
                                                    data-rating="<?= $i ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </form>
                                </div>
                                <?php
                                $comment_user_id = $_SESSION["user_id"];
                                $comment_post_id = $row["post_id"]
                                    ?>
                                <!-- Comment Form -->
                                <form class="mb-4" method="post" action="Add_comment.php">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($comment_user_id) ?>">
                                    <input type="hidden" name="post_id" value="<?= htmlspecialchars($comment_post_id) ?>">
                                    <div class="mb-3">
                                        <label for="commentText" class="form-label">Add your comment or tip:</label>
                                        <textarea class="form-control" id="commentText" name="commentText" rows="2"
                                            required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Post Comment</button>
                                </form>

                                <!-- Comment List -->
                                <h6>Comments (<?php echo $comments->num_rows; ?>) </h6>
                                <div class="comment-list">
                                    <?php
                                    while ($comment = $comments->fetch_assoc()) { ?>
                                        <div class="comment-box">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex align-items-center mb-1">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($comment["username"]); ?>
                                                        </h6>
                                                        <small
                                                            class="text-muted"><?php echo htmlspecialchars($comment["creation_datetime"]); ?></small>
                                                    </div>
                                                </div>

                                            </div>
                                            <p class="small mb-1"><?php echo htmlspecialchars($comment["content"]); ?></p>


                                        </div>


                                    <?php } ?>

                                </div>
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


    <!-- Floating Add Post Button with Hover Form -->
    <div class="add-post-container">
        <!-- Redirect to add_post.php when button is clicked -->
        <a href="Add_post.php" class="btn btn-primary rounded-circle add-post-btn">
            <i class="fas fa-plus fa-lg"></i>
        </a>
    </div>
</div>



<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script>
    function loadRecipeDetails(recipeId) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "../recipe_management_module/view_recipe.php?id=" + recipeId, true);
        xhr.onload = function () {
            if (this.status === 200) {
                document.getElementById("recipeDetails").innerHTML = this.responseText;
            }
        };
        xhr.send();
    }

    document.querySelectorAll('.rating-stars i').forEach(star => {
        const ratingContainer = star.parentElement;
        const existingRating = ratingContainer.getAttribute('data-existing-rating') || 0; // Get the user's previous rating

        star.addEventListener('mouseover', function () {

            const rating = parseInt(this.getAttribute('data-rating'), 10); // Convert to number
            const stars = ratingContainer.querySelectorAll('i');


            stars.forEach((s, index) => {
                s.className = index < rating ? 'fas fa-star' : 'far fa-star';
            });
        });

        star.addEventListener('mouseleave', function () {
            const stars = ratingContainer.querySelectorAll('i');

            stars.forEach((s, index) => {
                s.className = index < existingRating ? 'fas fa-star' : 'far fa-star';
            });
        });

        star.addEventListener('click', function () {

            let ratingContainer = this.closest('.rating-stars');
            let ratingForm = ratingContainer.closest('form');
            let rating = this.getAttribute('data-rating');
            let post_id = ratingContainer.getAttribute('data-post-id');
            let user_id = ratingContainer.getAttribute('data-user-id');
            let ratingInput = ratingForm.querySelector('#selectedRating');

            ratingInput.value = rating;
            console.log(user_id, post_id, ratingInput.value);
            ratingForm.submit();
        });
    });

    // Reset hover effect when mouse leaves rating area
    document.querySelectorAll('.modal .rating-stars').forEach(container => {
        container.addEventListener('mouseleave', function () {
            // Reset to initial state or show saved rating
            // This would normally check the user's saved rating
        });
    });
</script>
</body>

</html>
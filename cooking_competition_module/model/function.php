<?php
    function handleVote($submission_id, $user_id, $con) {
        // Check if the user has already voted for this submission
        $check_query = "SELECT * FROM competition_vote WHERE user_id = '$user_id' AND submission_id = '$submission_id'";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            return ['status' => 'error', 'message' => 'You have already voted for this submission!'];
        } else {
            // Insert the vote into the database
            $insert_query = "INSERT INTO competition_vote (user_id, submission_id, vote_date) VALUES ('$user_id', '$submission_id', NOW())";
            if (mysqli_query($con, $insert_query)) {
                return ['status' => 'success', 'message' => 'Your vote has been recorded successfully!'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to record your vote. Please try again.'];
            }
        }
    }
?>
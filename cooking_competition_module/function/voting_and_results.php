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

    function viewResult($competition_id, $submission_id, $con) {
        // Fetch the results for the given competition and submission
        $query = "SELECT * FROM competition_vote WHERE competition_id = '$competition_id' AND submission_id = '$submission_id'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            $votes = mysqli_fetch_assoc($result);
            return ['status' => 'success', 'data' => $votes];
        } else {
            return ['status' => 'error', 'message' => 'No results found for this competition.'];
        }
    }
?>
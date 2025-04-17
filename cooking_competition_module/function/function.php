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

    function addCompetition($competition_name, $image_path, $description, $start_date, $end_date, $rank, $prize, $con) {
        // Check if competition exists in the database using a prepared statement
        $check_query = "SELECT * FROM competition WHERE competition_name = ?";
        $stmt = mysqli_prepare($con, $check_query);
        if (!$stmt) {
            return ["status" => "error", "message" => "Failed to prepare query: " . mysqli_error($con)];
        }

        mysqli_stmt_bind_param($stmt, "s", $competition_name);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check_result) > 0) {
            return ["status" => "error", "message" => "Competition already exists!"];
        }
        // Insert the new competition into the database
        $insert_query = "INSERT INTO competition (competition_name, image_path, description, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $insert_query);
        if (!$stmt) {
            return ["status" => "error", "message" => "Failed to prepare insert query: " . mysqli_error($con)];
        }

        mysqli_stmt_bind_param($stmt, "sssss", $competition_name, $image_path, $description, $start_date, $end_date);

        if (mysqli_stmt_execute($stmt)) {
            // Get the last inserted competition ID
            $competition_id = mysqli_insert_id($con);

            // Insert rank and prize into competition_prize table
            $prize_query = "INSERT INTO competition_prize (competition_id, rank, prize) VALUES (?, ?, ?)";
            $stmt_prize = mysqli_prepare($con, $prize_query);
            if (!$stmt_prize) {
                return ["status" => "error", "message" => "Failed to prepare prize query: " . mysqli_error($con)];
            }

            mysqli_stmt_bind_param($stmt_prize, "iis", $competition_id, $rank, $prize);

            if (mysqli_stmt_execute($stmt_prize)) {
                return ["status" => "success", "message" => "Competition and prize added successfully!"];
            } else {
                return ["status" => "error", "message" => "Failed to add prize. Please try again."];
            }
        } else {
            return ["status" => "error", "message" => "Failed to add competition. Please try again."];
        }
    }
?>
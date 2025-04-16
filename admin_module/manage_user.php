<?php
session_start();
include("../user_module/auth.php");
require '../user_module/database.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    header("Location: ../index.php"); // Redirect non-admin users to the homepage
    exit();
}

// Fetch all non-admin users
$sql = "SELECT user_id, username, email FROM User WHERE isAdmin = 0";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-sm {
            border-radius: 20px;
        }

        .actions-column {
            display: flex;
            gap: 10px;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }

        .table-striped tbody tr:hover {
            background-color: #e9ecef;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #333;
        }

        .no-users {
            font-size: 1.25rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include("../navbar.php"); ?>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            alert("<?php echo $_SESSION['success']; ?>");
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="container my-5">
        <div class="table-container">
            <h2 class="page-title mb-4">Manage Users</h2>
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th scope="col">User ID</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col" class="text-center" style="width: 35%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="actions-column text-center">
                                    <!-- Modify Username -->
                                    <a href="edit_username.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit Username
                                    </a>
                                    <!-- Modify Password -->
                                    <a href="edit_password.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-key"></i> Change Password
                                    </a>
                                    <!-- Delete User -->
                                    <form action="delete_user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center no-users">No users found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>

</html>
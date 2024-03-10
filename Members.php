<?php
session_start();
include 'database.php';

function getUsersByPage($page, $limit)
{
    global $conn;

    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM user LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    $users = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    return $users;
}

$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Fetch total number of users for pagination
$sqlTotal = "SELECT COUNT(*) as total FROM user";
$resultTotal = $conn->query($sqlTotal);
$totalUsers = $resultTotal->fetch_assoc()['total'];

// Calculate total pages
$totalPages = ceil($totalUsers / $limit);

// Calculate the number of pages to show in the pagination
$visiblePages = 5;

$paginationStart = max(1, $page - floor($visiblePages / 2));
$paginationEnd = min($totalPages, $paginationStart + $visiblePages - 1);

$users = getUsersByPage($page, $limit);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Members</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

        <link rel="stylesheet" href="./member.css">

</head>

<body>
    <div class="container mt-5">
        <h2>Members</h2>

        <a href="add_user.php" class="btn btn-primary mb-3">Add User</a>

        <?php
        if (!empty($users)) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>User ID</th><th>Username</th><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone Number</th><th>Registration Date</th><th>Action</th></tr></thead>';
            echo '<tbody>';
            foreach ($users as $user) {
                echo '<tr>';
                echo "<td>{$user['user_id']}</td>";
                echo "<td>{$user['username']}</td>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['first_name']}</td>";
                echo "<td>{$user['last_name']}</td>";
                echo "<td>{$user['phone_number']}</td>";
                echo "<td>{$user['registration_date']}</td>";
                echo '<td>';
                echo '<a href="#" class="btn btn-info btn-sm btn-action" data-toggle="modal" data-target="#viewUserModal' . $user['user_id'] . '"><i class="fas fa-eye"></i></a>';
                echo ' <a href="edit_user.php?id=' . $user['user_id'] . '" class="btn btn-warning btn-sm btn-action"><i class="fas fa-edit"></i></a>';
                echo ' <a href="delete_user.php?id=' . $user['user_id'] . '" class="btn btn-danger btn-sm btn-action"><i class="fas fa-trash"></i></a>';
                echo '</td>';
                echo '</tr>';
                echo '<div class="modal fade" id="viewUserModal' . $user['user_id'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
                // Modal content
                echo '<div class="modal-dialog" role="document">';
                echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                echo '<h5 class="modal-title" id="exampleModalLabel">User Details</h5>';
                echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
                echo '<div class="modal-body">';
                // Populate modal content with user details here
                echo '<p><strong>User ID:</strong> ' . $user['user_id'] . '</p>';
                echo '<p><strong>Username:</strong> ' . $user['username'] . '</p>';
                echo '<p><strong>Email:</strong> ' . $user['email'] . '</p>';
                echo '<p><strong>First Name:</strong> ' . $user['first_name'] . '</p>';
                echo '<p><strong>Last Name:</strong> ' . $user['last_name'] . '</p>';
                echo '<p><strong>Phone Number:</strong> ' . $user['phone_number'] . '</p>';
                echo '<p><strong>Registration Date:</strong> ' . $user['registration_date'] . '</p>';
                echo '</div>';
                echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</tbody></table>';
            echo '</div>'; // Close table-responsive div

            // Pagination
            echo '<ul class="pagination justify-content-center">';
            if ($page > 1) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">&laquo; Previous</a></li>';
            }

            for ($i = $paginationStart; $i <= $paginationEnd; $i++) {
                echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
            }

            if ($page < $totalPages) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next &raquo;</a></li>';
            }

            echo '</ul>';
        } else {
            echo '<p>No members found.</p>';
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>
